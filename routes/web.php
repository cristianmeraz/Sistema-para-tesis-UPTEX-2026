<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebController;
use App\Http\Controllers\Web\TicketWebController;
use App\Http\Controllers\Web\UsuarioWebController;
use App\Http\Controllers\Web\ReporteWebController;
use App\Http\Controllers\Web\EncuestaWebController;

// --- INICIO ---
Route::get('/', function () { return redirect()->route('login'); });

// --- ENCUESTA DE SATISFACCIÓN (pública, sin autenticación) ---
Route::get('/encuesta/gracias', [EncuestaWebController::class, 'gracias'])->name('encuesta.gracias');
Route::get('/encuesta/{token}', [EncuestaWebController::class, 'show'])->name('encuesta.show');
Route::post('/encuesta/{token}', [EncuestaWebController::class, 'responder'])->name('encuesta.responder');

// --- AUTENTICACIÓN PÚBLICA ---
Route::get('/login', [WebController::class, 'showLogin'])->name('login');
Route::post('/login', [WebController::class, 'login'])->name('login.post');
Route::get('/register', [WebController::class, 'showRegister'])->name('register');
Route::post('/register', [WebController::class, 'register'])->name('register.post');

// --- RECUPERACIÓN DE CONTRASEÑA ---
Route::get('/forgot-password', [WebController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [WebController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [WebController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [WebController::class, 'resetPassword'])->name('password.update');

// --- RUTAS PROTEGIDAS ---
Route::middleware('web.auth')->group(function () {
    
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [WebController::class, 'logout'])->name('logout');
    Route::get('/perfil', [WebController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [WebController::class, 'updatePerfil'])->name('perfil.update');
    
    // ===== ENDPOINTS WEB-API PARA COMENTARIOS (CRUD) =====
    // IMPORTANTE: Prefijo /w/ en vez de /api/ para evitar conflicto con rutas Sanctum de api.php
    // ✅ FIX A-13: Rate limiting (60 req/min) en endpoints /w/
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/w/contadores', [TicketWebController::class, 'apiContadores'])->name('api.contadores');
        Route::get('/w/mis-tickets', [TicketWebController::class, 'apiMisTickets'])->name('api.mis-tickets');
        Route::get('/w/ticket/{id}', [TicketWebController::class, 'apiTicketDetalle'])->name('api.ticket.detalle');
        Route::get('/w/tickets/{id}/comentarios', [TicketWebController::class, 'apiComentariosTicket'])->name('api.ticket.comentarios');
        Route::post('/w/tickets/{id}/comentarios', [TicketWebController::class, 'apiCrearComentario'])->name('api.comentarios.crear');
        Route::put('/w/tickets/{ticketId}/comentarios/{comentarioId}', [TicketWebController::class, 'apiEditarComentario'])->name('api.comentarios.editar');
        Route::delete('/w/tickets/{ticketId}/comentarios/{comentarioId}', [TicketWebController::class, 'apiEliminarComentario'])->name('api.comentarios.eliminar');
    });
    
    // GESTIÓN DE TICKETS
    // Se excluye 'index': el Admin usa /admin/ver-tickets (protegido por web.admin)
    // Los técnicos usan /tickets-asignados; usuarios normales usan /mis-tickets
    // ✅ FIX U-6: Rate limiting en store() — máx 10 tickets por minuto por IP
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/tickets', [TicketWebController::class, 'store'])->name('tickets.store');
    });
    Route::resource('tickets', TicketWebController::class)->except(['index', 'store']);
    // cambiar-estado: accesible por Admin Y Técnico (la autorización se valida dentro del método)
    Route::post('/tickets/{id}/cambiar-estado', [TicketWebController::class, 'cambiarEstado'])->name('tickets.cambiar-estado');
    Route::get('/mis-tickets', [TicketWebController::class, 'misTickets'])->name('tickets.mis-tickets');

    // --- SOLO ADMINISTRADORES (UPTEX) ---
    Route::middleware('web.admin')->group(function () {
        Route::get('/admin/ver-tickets', [TicketWebController::class, 'index'])->name('tickets.index');

        // Acciones exclusivas del administrador sobre tickets
        Route::post('/tickets/{id}/asignar-tecnico', [TicketWebController::class, 'asignarTecnico'])->name('tickets.asignar-tecnico');
        Route::post('/tickets/{id}/cambiar-prioridad', [TicketWebController::class, 'cambiarPrioridad'])->name('tickets.cambiar-prioridad');

        Route::resource('usuarios', UsuarioWebController::class);
        Route::post('/usuarios/{id}/toggle-activo', [UsuarioWebController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
        Route::get('/usuarios-importar', [UsuarioWebController::class, 'importForm'])->name('usuarios.import.form');
        Route::post('/usuarios-importar', [UsuarioWebController::class, 'importStore'])->name('usuarios.import.store');

        Route::get('/reportes', [ReporteWebController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/refresh-stats', [ReporteWebController::class, 'refreshStats'])->name('reportes.refresh-stats');
        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/por-fecha', [ReporteWebController::class, 'porFecha'])->name('por-fecha');
            Route::get('/rendimiento', [ReporteWebController::class, 'rendimiento'])->name('rendimiento');
            Route::get('/exportar', [ReporteWebController::class, 'exportar'])->name('exportar');
        });

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/tecnicos/crear', [UsuarioWebController::class, 'createTecnico'])->name('tecnicos.create');
            Route::post('/tecnicos/guardar', [UsuarioWebController::class, 'storeTecnico'])->name('tecnicos.store');
            Route::get('/usuarios/crear', [UsuarioWebController::class, 'createUsuario'])->name('usuarios.create');
            Route::post('/usuarios/guardar', [UsuarioWebController::class, 'storeUsuario'])->name('usuarios.store');
        });
    });

    // --- SOLO TÉCNICOS ---
    Route::middleware('web.tecnico')->group(function () {
        Route::get('/tickets-asignados', [TicketWebController::class, 'asignados'])->name('tickets.asignados');
        Route::get('/historial-tickets', [TicketWebController::class, 'misTicketsHistorial'])->name('tickets.historial');
        Route::get('/tecnico/boleta-ticket/{id}', [TicketWebController::class, 'verFichaTecnica'])->name('tecnicos.ver-ticket');
    });
});