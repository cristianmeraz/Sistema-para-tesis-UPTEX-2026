@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('no_header_title', true)

@section('content')

@php
    $rolNombre = $usuario['rol']['nombre'] ?? '';
    $esAdmin   = $rolNombre === 'Administrador';
    $esTecnico = str_contains($rolNombre, 'cnico');

    if ($esAdmin) {
        $rolColor     = '#1e3a5f';
        $rolColorLight= 'rgba(30,58,95,.12)';
        $rolIcon      = 'bi-shield-fill';
    } elseif ($esTecnico) {
        $rolColor     = '#0369a1';
        $rolColorLight= 'rgba(3,105,161,.12)';
        $rolIcon      = 'bi-tools';
    } else {
        $rolColor     = '#0891b2';
        $rolColorLight= 'rgba(8,145,178,.12)';
        $rolIcon      = 'bi-person-fill';
    }

    $iniciales = strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1) . substr($usuario['apellido'] ?? '', 0, 1));
@endphp

<style>
    body { background: #f3f4f6; }

    /* ── Cabecera ─────────────────────────── */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        padding: 2rem 2.5rem;
        margin-bottom: 1.8rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .page-header h1 { color: #fff; font-size: 1.7rem; font-weight: 700; margin: 0; }
    .page-header p  { color: rgba(255,255,255,.72); font-size: .9rem; margin: .2rem 0 0; }

    /* ── Tarjetas ─────────────────────────── */
    .prof-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(30,58,95,.08);
        overflow: hidden;
        height: 100%;
    }
    .prof-card-header {
        background: #f8faff;
        border-bottom: 1px solid #e8edf5;
        padding: .9rem 1.4rem;
        font-weight: 700;
        font-size: .9rem;
        color: #1e3a5f;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .prof-card-body { padding: 1.8rem 1.5rem; }

    /* ── Avatar / panel izquierdo ─────────── */
    .avatar-wrap {
        text-align: center;
        padding: 2rem 1.5rem 1.5rem;
    }
    .avatar-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: {{ $rolColorLight }};
        border: 4px solid {{ $rolColor }};
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.2rem;
        font-size: 2.6rem;
        color: {{ $rolColor }};
    }
    .avatar-name {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e3a5f;
        margin-bottom: .3rem;
    }
    .avatar-email {
        font-size: .85rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }
    .role-badge {
        display: inline-block;
        padding: .35rem 1.1rem;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 700;
        background: {{ $rolColorLight }};
        color: {{ $rolColor }};
        border: 1.5px solid {{ $rolColor }};
    }

    /* ── Info de cuenta ───────────────────── */
    .info-row {
        padding: .75rem 0;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        flex-direction: column;
        gap: .18rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: .78rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .04em; }
    .info-value { font-size: .95rem; font-weight: 600; color: #1f2937; }

    /* ── Formulario ───────────────────────── */
    .form-section-title {
        font-size: .95rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .45rem;
    }
    .field-label {
        font-size: .83rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: .4rem;
        display: block;
    }
    .field-input {
        width: 100%;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        padding: .65rem .95rem;
        font-size: .9rem;
        color: #1f2937;
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .field-input:focus {
        border-color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(29,78,216,.1);
    }
    .field-input.is-invalid { border-color: #dc2626; }
    .invalid-feedback { font-size: .8rem; color: #dc2626; margin-top: .3rem; }

    .divider {
        border: none;
        border-top: 1.5px solid #e5e7eb;
        margin: 1.5rem 0;
    }
    .btn-save {
        width: 100%;
        background: #1e3a5f;
        color: #fff;
        border: none;
        padding: .85rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: .97rem;
        cursor: pointer;
        transition: background .2s, transform .15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
    }
    .btn-save:hover { background: #1d4ed8; transform: translateY(-1px); }
</style>

{{-- ── CABECERA ──────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1><i class="bi bi-person-circle" style="margin-right:.5rem;"></i>Mi Perfil</h1>
        <p>Administra tu informacion personal y contrasena</p>
    </div>
</div>

<div class="container-fluid">

    {{-- Alertas --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert"
         style="border-radius:10px; border-left:4px solid #16a34a; background:#f0fdf4; color:#15803d; font-weight:600;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert"
         style="border-radius:10px; border-left:4px solid #dc2626; background:#fef2f2; color:#b91c1c; font-weight:600;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>Por favor corrige los errores:
        <ul class="mb-0 mt-1" style="font-weight:400; font-size:.9rem;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- ── COLUMNA IZQUIERDA ──────────────── --}}
        <div class="col-lg-4 d-flex flex-column gap-4">

            {{-- Tarjeta del usuario --}}
            <div class="prof-card">
                <div class="avatar-wrap">
                    <div class="avatar-icon">
                        <i class="bi {{ $rolIcon }}"></i>
                    </div>
                    <div class="avatar-name">{{ $usuario['nombre'] }} {{ $usuario['apellido'] }}</div>
                    <div class="avatar-email">{{ $usuario['correo'] }}</div>
                    <span class="role-badge">
                        <i class="bi {{ $rolIcon }} me-1"></i>{{ $rolNombre }}
                    </span>
                </div>
            </div>

            {{-- Tarjeta de informacion de cuenta --}}
            <div class="prof-card">
                <div class="prof-card-header">
                    <i class="bi bi-info-circle-fill"></i> Informacion de cuenta
                </div>
                <div class="prof-card-body">
                    <div class="info-row">
                        <span class="info-label">Miembro desde</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            @if($esTecnico) Ultimo acceso @else Ultima actualizacion @endif
                        </span>
                        <span class="info-value">
                            @if($esTecnico)
                                @if($usuario['last_login'])
                                    {{ \Carbon\Carbon::parse($usuario['last_login'])->setTimezone('America/Mexico_City')->format('d/m/Y H:i') }}
                                @else
                                    Sin acceso aun
                                @endif
                            @else
                                {{ \Carbon\Carbon::parse($usuario['updated_at'])->diffForHumans() }}
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rol</span>
                        <span class="info-value" style="color:{{ $rolColor }};">{{ $rolNombre }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── COLUMNA DERECHA ────────────────── --}}
        <div class="col-lg-8">
            <div class="prof-card">
                <div class="prof-card-header">
                    <i class="bi bi-pencil-square"></i> Editar Informacion
                </div>
                <div class="prof-card-body">
                    <form action="{{ route('perfil.update') }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        {{-- Datos personales --}}
                        <div class="form-section-title">
                            <i class="bi bi-person"></i> Datos personales
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="field-label" for="nombre">Nombre</label>
                                <input type="text"
                                       class="field-input @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre"
                                       value="{{ old('nombre', $usuario['nombre']) }}"
                                       autocomplete="off">
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="field-label" for="apellido">Apellido</label>
                                <input type="text"
                                       class="field-input @error('apellido') is-invalid @enderror"
                                       id="apellido" name="apellido"
                                       value="{{ old('apellido', $usuario['apellido']) }}"
                                       autocomplete="off">
                                @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-1">
                            <label class="field-label" for="correo">Correo Electronico</label>
                            <input type="email"
                                   class="field-input @error('correo') is-invalid @enderror"
                                   id="correo" name="correo"
                                   value="{{ old('correo', $usuario['correo']) }}"
                                   autocomplete="off">
                            @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr class="divider">

                        {{-- Cambiar contrasena --}}
                        <div class="form-section-title">
                            <i class="bi bi-lock"></i> Cambiar Contrasena
                            <span style="font-size:.78rem; font-weight:400; color:#9ca3af;">(dejar en blanco para no cambiar)</span>
                        </div>

                        <div class="row g-3 mb-1">
                            <div class="col-md-6">
                                <label class="field-label" for="password">Nueva Contrasena</label>
                                <input type="text"
                                       class="field-input @error('password') is-invalid @enderror"
                                       id="password" name="password"
                                       autocomplete="off" spellcheck="false">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="field-label" for="password_confirmation">Confirmar Contrasena</label>
                                <input type="text"
                                       class="field-input"
                                       id="password_confirmation" name="password_confirmation"
                                       autocomplete="off" spellcheck="false">
                            </div>
                        </div>

                        <hr class="divider">

                        <button type="submit" class="btn-save">
                            <i class="bi bi-check-circle-fill"></i> Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}
</div>{{-- /container-fluid --}}

<script>
    // Convertir campos de texto a password al interactuar
    const passwordField = document.getElementById('password');
    const confirmField  = document.getElementById('password_confirmation');
    [passwordField, confirmField].forEach(field => {
        ['focus','keydown'].forEach(evt => {
            field.addEventListener(evt, function() {
                if (this.type === 'text') this.type = 'password';
            });
        });
    });
</script>

@endsection
