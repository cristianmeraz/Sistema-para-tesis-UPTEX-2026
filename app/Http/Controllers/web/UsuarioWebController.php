<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;

class UsuarioWebController extends Controller
{
    /**
     * Lista de usuarios - CON CACHÉ Y PAGINACIÓN OPTIMIZADA
     */
    public function index(Request $request)
    {
        $query = Usuario::with('rol')->orderBy('nombre');
        
        // Filtrar por rol
        if ($request->filled('id_rol')) {
            $query->where('id_rol', $request->id_rol);
        }
        
        // Filtrar por estado (activo/inactivo)
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo == '1' ? true : false);
        }
        
        $usuarios = $query->paginate(50)->withQueryString();
        // Caché de roles: 60 minutos
        $roles = Cache::remember('roles_catalogo', 60, function() { return Rol::orderBy('nombre')->get(['id_rol', 'nombre']); });

        // Stats globales (sin filtros) para los KPI cards
        $stats = Cache::remember('usuarios_stats', 5, function() {
            return [
                'total'    => Usuario::count(),
                'activos'  => Usuario::where('activo', true)->count(),
                'tecnicos' => Usuario::whereHas('rol', fn($q) => $q->where('nombre', 'Técnico'))->count(),
                'admins'   => Usuario::whereHas('rol', fn($q) => $q->where('nombre', 'Administrador'))->count(),
                'normales' => Usuario::whereHas('rol', fn($q) => $q->where('nombre', 'like', 'Usuario%'))->count(),
            ];
        });

        // IDs de rol directamente desde la BD (sin depender de comparación de strings en la vista)
        $rolIdTecnico = Rol::where('nombre', 'like', '%cnico')->value('id_rol');
        $rolIdAdmin   = Rol::where('nombre', 'Administrador')->value('id_rol');
        $rolIdNormal  = Rol::where('nombre', 'like', 'Usuario%')->value('id_rol');
        $rolIds = ['tecnico' => $rolIdTecnico, 'admin' => $rolIdAdmin, 'normal' => $rolIdNormal];

        return view('usuarios.index', compact('usuarios', 'roles', 'stats', 'rolIds'));
    }

    /**
     * EL BOTÓN AZUL (EL OJO): Ver detalle del usuario (Se mantiene intacta)
     */
    public function show($id)
    {
        try {
            $u = Usuario::with(['rol', 'tickets.estado', 'ticketsAsignados.estado'])->findOrFail($id);

            $usuario = [
                'id_usuario' => $u->id_usuario,
                'nombre'     => $u->nombre,
                'apellido'   => $u->apellido,
                'correo'     => $u->correo,
                'activo'     => $u->activo,
                'created_at' => $u->created_at,
                'updated_at' => $u->updated_at,
                'rol' => [
                    'nombre' => $u->rol->nombre ?? 'N/A'
                ],
                'tickets' => $u->tickets->map(function($t) {
                    return [
                        'id_ticket'      => $t->id_ticket,
                        'titulo'         => $t->titulo,
                        'fecha_creacion' => $t->fecha_creacion,
                        'estado' => [
                            'nombre' => $t->estado->nombre ?? 'N/A',
                            'tipo'   => $t->estado->tipo ?? 'abierto'
                        ]
                    ];
                })->toArray(),
                'tickets_asignados' => $u->ticketsAsignados ?? []
            ];

            return view('usuarios.show', compact('usuario'));

        } catch (\Exception $e) {
            \Log::error("Error al mostrar usuario: " . $e->getMessage());
            return redirect()->route('usuarios.index')->with('error', 'No se pudo cargar la información.');
        }
    }

    /**
     * EL LÁPIZ: Formulario de edición (Se mantiene intacta)
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        $roles = Rol::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Procesar actualización (Se mantiene intacta)
     */
    public function update(Request $request, $id)
    {
        $u = Usuario::findOrFail($id);

        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'correo'   => 'required|email|max:150|unique:usuarios,correo,' . $u->id_usuario . ',id_usuario',
            'id_rol'   => 'required|integer|exists:roles,id_rol',
            'activo'   => 'sometimes|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Solo actualizar campos permitidos explicitamente (no $request->all())
        $u->nombre   = $request->nombre;
        $u->apellido = $request->apellido;
        $u->correo   = $request->correo;
        $u->id_rol   = $request->id_rol;
        $u->activo   = $request->boolean('activo', $u->activo);

        if ($request->filled('password')) {
            $u->password = Hash::make($request->password);
        }
        $u->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Botón de activar/desactivar (Se mantiene intacta)
     */
    public function toggleActivo($id)
    {
        $u = Usuario::findOrFail($id);
        $u->activo = !$u->activo;
        $u->save();

        // ✅ FIX A-10: Forzar re-verificación de sesión si el usuario fue desactivado
        if (!$u->activo) {
            Cache::put('force_auth_check_' . $u->id_usuario, true, 600);
        }

        return back()->with('success', 'Estado de usuario actualizado.');
    }

    /**
     * EL BOTÓN ROJO (BASURA): Eliminar usuario
     */
    public function destroy($id)
    {
        try {
            // Buscar el usuario a eliminar
            $usuario = Usuario::find($id);
            
            if (!$usuario) {
                return back()->with('error', 'El usuario no existe.');
            }
            
            // Obtener el ID del usuario actual desde la sesión
            $usuarioActualId = session('usuario_id');
            
            // No permitir eliminar al usuario actual
            if ($usuarioActualId && $usuarioActualId == $id) {
                return back()->with('error', 'No puedes eliminar tu propio usuario.');
            }
            
            // Eliminar comentarios asociados
            $usuario->comentarios()->delete();
            
            // Desvincular tickets asignados
            $usuario->ticketsAsignados()->update(['tecnico_asignado_id' => null]);
            
            // Eliminar tickets creados por el usuario
            $usuario->tickets()->delete();
            
            // Eliminar el usuario
            $usuario->delete();
            
            return back()->with('success', 'Usuario eliminado correctamente.');
            
        } catch (\Exception $e) {
            \Log::error("Error al eliminar usuario: " . $e->getMessage() . " | ID: " . $id);
            return back()->with('error', 'Error al eliminar el usuario.');
        }
    }

    // --- LAS SIGUIENTES 4 FUNCIONES SON LAS QUE ARREGLAN LOS BOTONES DE CREACIÓN ---

    /**
     * Vista para crear Usuario Normal
     */
    public function createUsuario()
    {
        $areas = \App\Models\Area::orderBy('nombre')->get();
        return view('usuarios.create', compact('areas'));
    }

    /**
     * Guardar Usuario Normal (Rol ID 3)
     */
    public function storeUsuario(Request $request)
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-\']+$/u'],
            'apellido' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-\']+$/u'],
            'correo'   => [
                'required', 'string', 'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                'unique:usuarios,correo',
            ],
            'password' => [
                'required', 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'area_id'  => ['nullable', 'integer', 'exists:areas,id_area'],
        ], [
            'nombre.regex'              => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex'            => 'El apellido solo puede contener letras y espacios.',
            'correo.regex'              => 'Ingresa un correo válido con dominio completo (ej: usuario@gmail.com).',
            'correo.unique'             => 'Ese correo ya está registrado.',
            'password.password.mixed'   => 'La contraseña debe tener al menos una letra mayúscula y una minúscula.',
            'password.password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.password.symbols' => 'La contraseña debe contener al menos un símbolo (ej: #, @, !, $, %).',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        Usuario::create([
            'nombre'   => trim($request->nombre),
            'apellido' => trim($request->apellido),
            'correo'   => strtolower(trim($request->correo)),
            'password' => Hash::make($request->password),
            'id_rol'   => Rol::where('nombre', 'like', 'Usuario%')->value('id_rol') ?? 3, // ✅ FIX A-11
            'area_id'  => $request->area_id ?: null,
            'activo'   => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Vista para crear Técnico
     */
    public function createTecnico()
    {
        return view('tecnicos.create');
    }

    /**
     * Guardar Técnico (Rol ID 2)
     */
    public function storeTecnico(Request $request)
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-\']+$/u'],
            'apellido' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-\']+$/u'],
            'correo'   => [
                'required', 'string', 'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                'unique:usuarios,correo',
            ],
            'password' => [
                'required', 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'nombre.regex'              => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex'            => 'El apellido solo puede contener letras y espacios.',
            'correo.regex'              => 'Ingresa un correo válido con dominio completo (ej: usuario@gmail.com).',
            'correo.unique'             => 'Ese correo ya está registrado.',
            'password.password.mixed'   => 'La contraseña debe tener al menos una letra mayúscula y una minúscula.',
            'password.password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.password.symbols' => 'La contraseña debe contener al menos un símbolo (ej: #, @, !, $, %).',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        Usuario::create([
            'nombre'   => trim($request->nombre),
            'apellido' => trim($request->apellido),
            'correo'   => strtolower(trim($request->correo)),
            'password' => Hash::make($request->password),
            'id_rol'   => Rol::where('nombre', 'Técnico')->value('id_rol') ?? 2, // ✅ FIX A-11
            'activo'   => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Técnico creado correctamente.');
    }
}