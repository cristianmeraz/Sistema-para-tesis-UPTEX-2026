@extends('layouts.app')

@section('title', 'Importar Usuarios desde CSV')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width:860px; margin:0 auto;">

    {{-- Cabecera --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <div>
            <h4 class="mb-0 fw-bold" style="color:#1e3a5f;">
                <i class="bi bi-file-earmark-arrow-up me-2"></i>Importar Usuarios desde CSV
            </h4>
            <small class="text-muted">Carga masiva de cuentas de "Usuario Normal"</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="row g-4">

        {{-- Formulario --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-uppercase text-muted small">
                        <i class="bi bi-upload me-1"></i>Subir archivo CSV
                    </h6>

                    {{-- Botones de descarga de plantilla CSV --}}
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="{{ route('usuarios.import.csv', ['tipo' => 'ejemplo']) }}"
                           class="btn btn-sm btn-outline-success fw-semibold" download>
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                            Descargar CSV con ejemplo (2 filas llenas)
                        </a>
                        <a href="{{ route('usuarios.import.csv', ['tipo' => 'vacio']) }}"
                           class="btn btn-sm btn-outline-secondary fw-semibold" download>
                            <i class="bi bi-file-earmark-plus me-1"></i>
                            Descargar plantilla vacía (solo encabezados)
                        </a>
                    </div>

                    <form method="POST" action="{{ route('usuarios.import.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="csv_file" class="form-label fw-semibold">Archivo CSV <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('csv_file') is-invalid @enderror"
                                id="csv_file" name="csv_file" accept=".csv,.txt" required>
                            <div class="form-text">Máximo 2 MB. Formato: <code>.csv</code> separado por comas.</div>
                            @error('csv_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn w-100 fw-bold"
                            style="background-color:#1e3a5f; color:#fff; border-radius:8px; padding:.75rem;">
                            <i class="bi bi-cloud-upload me-2"></i>Importar usuarios
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Instrucciones --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px; background:#f8fafc;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-uppercase text-muted small">
                        <i class="bi bi-info-circle me-1"></i>Instrucciones
                    </h6>

                    <p class="text-muted small mb-2">El archivo CSV debe tener exactamente <strong>5 columnas</strong> en el siguiente orden:</p>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered mb-0" style="font-size:.82rem;">
                            <thead class="table-dark">
                                <tr>
                                    <th>Columna</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td><code>nombre</code></td><td>Nombre(s)</td></tr>
                                <tr><td><code>apellido</code></td><td>Apellido(s)</td></tr>
                                <tr><td><code>correo</code></td><td>Email único</td></tr>
                                <tr><td><code>password</code></td><td>Contraseña (texto plano)</td></tr>
                                <tr><td><code>area_id</code></td><td>ID del área</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small mb-2">Ejemplo de contenido:</p>
                    <pre class="p-2 rounded small" style="background:#1e293b; color:#e2e8f0; font-size:.75rem; overflow-x:auto;">nombre,apellido,correo,password,area_id
Juan,García,jgarcia@uptex.edu.mx,Pass1234!,1
María,López,mlopez@uptex.edu.mx,Segura#99,2</pre>

                    <p class="text-muted small mb-1"><strong>Áreas disponibles:</strong></p>
                    <ul class="small text-muted mb-0 ps-3">
                        @foreach($areas as $area)
                            <li>ID {{ $area->id_area }} — {{ $area->nombre }}</li>
                        @endforeach
                    </ul>

                    <div class="alert alert-warning mt-3 mb-0 py-2 px-3" style="font-size:.8rem;">
                        <i class="bi bi-shield-exclamation me-1"></i>
                        Las contraseñas se guardarán <strong>cifradas</strong> con bcrypt.
                        Los correos duplicados se omitirán automáticamente.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
