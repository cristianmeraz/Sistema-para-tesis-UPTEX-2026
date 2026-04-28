<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Area;
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

        // Protección: el administrador no puede cambiar su propio rol
        if ((int)$u->id_usuario === (int)session('usuario_id') &&
            (int)$request->id_rol !== (int)$u->id_rol) {
            return back()->with('error', 'No puedes cambiar tu propio rol.')->withInput();
        }

        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'correo'   => 'required|email|max:150|unique:usuarios,correo,' . $u->id_usuario . ',id_usuario',
            'id_rol'   => 'required|integer|exists:roles,id_rol',
            'activo'   => 'sometimes|boolean',
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
            'password.password.mixed'   => 'La contraseña debe tener al menos una letra mayúscula y una minúscula.',
            'password.password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.password.symbols' => 'La contraseña debe contener al menos un símbolo (ej: #, @, !, $, %).',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        // Solo actualizar campos permitidos explicitamente (no $request->all())
        $rolAnterior = $u->id_rol;
        $u->nombre   = $request->nombre;
        $u->apellido = $request->apellido;
        $u->correo   = $request->correo;
        $u->id_rol   = $request->id_rol;
        $u->activo   = $request->boolean('activo', $u->activo);

        if ($request->filled('password')) {
            // Anti-repetición: no puede ser igual a la contraseña actual
            if (Hash::check($request->password, $u->password)) {
                return back()->withErrors(['password' => 'La nueva contraseña no puede ser igual a la contraseña actual.'])->withInput();
            }
            $u->password = Hash::make($request->password);
        }
        $u->save();

        // Si el rol cambió, forzar re-verificación de sesión en el próximo request del usuario
        if ((int)$request->id_rol !== (int)$rolAnterior) {
            Cache::put('force_auth_check_' . $u->id_usuario, true, 600);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Botón de activar/desactivar (Se mantiene intacta)
     */
    public function toggleActivo($id)
    {
        $u = Usuario::findOrFail($id);

        // No permitir que el administrador se desactive a sí mismo
        if ($u->id_usuario == session('usuario_id')) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $estabaInactivo = !$u->activo;
        $u->activo = !$u->activo;
        $u->save();

        // Forzar re-verificación de sesión si el usuario fue desactivado
        if (!$u->activo) {
            Cache::put('force_auth_check_' . $u->id_usuario, true, 600);
        }

        // Al reactivar cuenta, resetear el contador de resets de contraseña
        if ($u->activo && $estabaInactivo) {
            $u->password_reset_count = 0;
            $u->save();
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

    // LAS SIGUIENTES 4 FUNCIONES SON LAS QUE ARREGLAN LOS BOTONES DE CREACIÓN

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
            'id_rol'   => Rol::where('nombre', 'like', 'Usuario%')->value('id_rol') ?? 3, //  FIX A-11
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
            'id_rol'   => Rol::where('nombre', 'Técnico')->value('id_rol') ?? 2, //  FIX A-11
            'activo'   => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Técnico creado correctamente.');
    }

    // 
    // IMPORTADOR CSV DE USUARIOS
    // Columnas esperadas: nombre,apellido,correo,password,area_id
    // 

    public function importForm()
    {
        $areas = Area::orderBy('nombre')->get(['id_area', 'nombre']);
        return view('usuarios.import', compact('areas'));
    }

    /**
     * Descarga un CSV de plantilla.
     * tipo=ejemplo → 2 filas de muestra llenas
     * tipo=vacio   → solo encabezados para llenar
     */
    public function downloadCsvEjemplo(Request $request)
    {
        $tipo = $request->query('tipo', 'ejemplo');

        // Obtener las 2 primeras áreas reales para el ejemplo
        $areas = Area::orderBy('id_area')->limit(2)->get(['id_area', 'nombre']);
        $area1 = $areas->get(0);
        $area2 = $areas->get(1) ?? $area1;

        $encabezado = "nombre,apellido,correo,password,area_id\n";

        if ($tipo === 'vacio') {
            $contenido = $encabezado;
            $nombreArchivo = 'plantilla_usuarios.csv';
        } else {
            $id1 = $area1->id_area ?? 1;
            $id2 = $area2->id_area ?? 2;
            $contenido  = $encabezado;
            $contenido .= "Juan,Garcia,jgarcia@uptex.edu.mx,Contrasena1#,{$id1}\n";
            $contenido .= "Maria,Lopez,mlopez@uptex.edu.mx,Segura2@99,{$id2}\n";
            $nombreArchivo = 'ejemplo_usuarios.csv';
        }

        return response($contenido, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'csv_file.required' => 'Debes seleccionar un archivo CSV.',
            'csv_file.mimes'    => 'El archivo debe ser CSV (.csv o .txt).',
            'csv_file.max'      => 'El archivo no puede superar 2 MB.',
        ]);

        $rolNormal = Rol::where('nombre', 'like', 'Usuario%')->value('id_rol') ?? 3;
        $areaIds   = Area::pluck('id_area')->toArray();

        $handle   = fopen($request->file('csv_file')->getRealPath(), 'r');
        $fila     = 0;
        $creados  = 0;
        $errores  = [];

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $fila++;

            // Saltar cabecera
            if ($fila === 1 && !is_numeric($row[0] ?? '')) {
                continue;
            }

            // Verificar columnas mínimas
            if (count($row) < 5) {
                $errores[] = "Fila {$fila}: faltan columnas (se necesitan nombre, apellido, correo, password, area_id).";
                continue;
            }

            [$nombre, $apellido, $correo, $password, $areaId] = array_map('trim', $row);

            // Validaciones básicas por fila
            if (empty($nombre) || empty($apellido) || empty($correo) || empty($password)) {
                $errores[] = "Fila {$fila}: campos vacíos (nombre, apellido, correo o password).";
                continue;
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "Fila {$fila}: correo inválido «{$correo}».";
                continue;
            }
            if (Usuario::where('correo', strtolower($correo))->exists()) {
                $errores[] = "Fila {$fila}: correo «{$correo}» ya está registrado (omitido).";
                continue;
            }
            $areaIdInt = (int) $areaId;
            if (!in_array($areaIdInt, $areaIds)) {
                $errores[] = "Fila {$fila}: area_id {$areaId} no existe (omitido).";
                continue;
            }

            Usuario::create([
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'correo'   => strtolower($correo),
                'password' => Hash::make($password),
                'id_rol'   => $rolNormal,
                'area_id'  => $areaIdInt,
                'activo'   => true,
            ]);
            $creados++;
        }

        fclose($handle);

        $msg = "Importación completada: {$creados} usuario(s) creado(s).";
        if (count($errores)) {
            $msg .= ' Filas con errores: ' . implode(' | ', array_slice($errores, 0, 10));
        }

        return redirect()->route('usuarios.index')
            ->with('success', $msg);
    }
}