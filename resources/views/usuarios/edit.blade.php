@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<style>
    body { background: #f1f5f9; }

    /* === HEADER BANNER === */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem;
        color: white; display: flex; justify-content: space-between;
        align-items: center; flex-wrap: wrap; gap: 1rem;
        box-shadow: 0 8px 24px rgba(30,58,95,.25);
    }
    .page-header-title { font-size: 1.5rem; font-weight: 800; margin: 0; }
    .page-header-sub   { font-size: .85rem; opacity: .8; margin: .2rem 0 0; }
    .btn-hdr {
        padding: .55rem 1.2rem; border-radius: 8px; font-weight: 700;
        font-size: .85rem; display: inline-flex; align-items: center;
        gap: .4rem; text-decoration: none; transition: all .2s ease;
        border: none; cursor: pointer; white-space: nowrap;
    }
    .btn-hdr-white { background: white; color: #1e3a5f; }
    .btn-hdr-white:hover { background: #dbeafe; color: #1e3a5f; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.15); }

    /* === PROFILE STRIP === */
    .profile-strip {
        background: white; border: 1px solid #dbeafe; border-radius: 14px;
        padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
        box-shadow: 0 2px 8px rgba(30,58,95,.06);
    }
    .avatar-md {
        width: 52px; height: 52px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; color: white; font-size: 1.3rem; flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(0,0,0,.12);
    }
    .av-admin   { background: linear-gradient(135deg, #1e3a5f, #1d4ed8); }
    .av-tecnico { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
    .av-normal  { background: linear-gradient(135deg, #0891b2, #06b6d4); }
    .av-otro    { background: linear-gradient(135deg, #475569, #64748b); }
    .strip-info { flex: 1; min-width: 140px; }
    .strip-name { font-size: 1.05rem; font-weight: 800; color: #1e293b; margin: 0; }
    .strip-sub  { font-size: .8rem; color: #64748b; margin: 0; }
    .strip-tags { display: flex; gap: .4rem; flex-wrap: wrap; align-items: center; margin-left: auto; }
    .role-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .75rem; border-radius: 20px;
        font-size: .75rem; font-weight: 700; white-space: nowrap;
    }
    .rb-admin   { background: #1e3a5f; color: white; }
    .rb-tecnico { background: #4f46e5; color: white; }
    .rb-normal  { background: #0891b2; color: white; }
    .rb-otro    { background: #475569; color: white; }
    .status-badge {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .25rem .7rem; border-radius: 8px; font-size: .75rem; font-weight: 700;
    }
    .sb-active   { background: #d1fae5; color: #065f46; }
    .sb-inactive { background: #fee2e2; color: #991b1b; }

    /* === FORM CARD === */
    .form-card {
        background: white; border: 1px solid #dbeafe; border-radius: 14px;
        overflow: hidden; max-width: 780px; margin: 0 auto;
        box-shadow: 0 2px 8px rgba(30,58,95,.06);
    }
    .form-card-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        color: white; padding: .9rem 1.5rem;
        display: flex; align-items: center; gap: .5rem;
        font-weight: 700; font-size: .9rem;
    }
    .form-body { padding: 1.5rem; }

    /* === SECTION DIVIDER === */
    .form-section {
        font-size: .75rem; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .05em;
        padding-bottom: .5rem; border-bottom: 2px solid #dbeafe;
        margin: 1.5rem 0 1rem; display: flex; align-items: center; gap: .4rem;
    }
    .form-section:first-child { margin-top: 0; }

    /* === FORM FIELDS === */
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .field { margin-bottom: 1.1rem; }
    .field-label {
        display: block; font-size: .78rem; font-weight: 700;
        color: #1e3a5f; margin-bottom: .35rem; text-transform: uppercase;
        letter-spacing: .03em;
    }
    .field-label .req { color: #ef4444; }
    .field-input {
        width: 100%; border: 1.5px solid #dbeafe; border-radius: 8px;
        padding: .6rem 1rem; font-size: .9rem; color: #1e293b;
        background: #f8fafc; transition: all .2s;
    }
    .field-input:focus {
        border-color: #1d4ed8; outline: none;
        box-shadow: 0 0 0 3px rgba(29,78,216,.1); background: white;
    }
    .field-input.is-invalid { border-color: #ef4444; }
    .field-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.1); }
    .field-hint { font-size: .75rem; color: #94a3b8; margin-top: .3rem; display: flex; align-items: flex-start; gap: .3rem; }
    .field-hint i { margin-top: .1rem; }
    .invalid-feedback { font-size: .78rem; }

    /* === SELECT === */
    .field-select {
        width: 100%; border: 1.5px solid #dbeafe; border-radius: 8px;
        padding: .6rem 1rem; font-size: .9rem; color: #1e293b;
        background: #f8fafc; transition: all .2s; cursor: pointer;
        appearance: auto;
    }
    .field-select:focus { border-color: #1d4ed8; outline: none; box-shadow: 0 0 0 3px rgba(29,78,216,.1); background: white; }

    /* === PASSWORD TOGGLE === */
    .pass-wrap { position: relative; }
    .pass-wrap .field-input { padding-right: 2.8rem; }
    .pass-toggle-btn {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: #94a3b8; cursor: pointer;
        padding: .25rem; font-size: 1.1rem; line-height: 1;
    }
    .pass-toggle-btn:hover { color: #1d4ed8; }

    /* === SWITCH === */
    .switch-row {
        display: flex; align-items: center; gap: .6rem;
        padding: .6rem 1rem; border-radius: 8px;
        background: #f8fafc; border: 1.5px solid #dbeafe;
        height: 42px; box-sizing: border-box;
    }
    .switch-row .form-check-input { width: 2.5rem; height: 1.25rem; cursor: pointer; }
    .switch-row .form-check-input:checked { background-color: #1d4ed8; border-color: #1d4ed8; }
    .switch-label { font-size: .88rem; font-weight: 600; color: #1e293b; cursor: pointer; }

    /* === META STRIP (bottom) === */
    .meta-strip {
        display: flex; gap: 1.5rem; flex-wrap: wrap;
        padding: .8rem 1rem; background: #f8fafc;
        border-radius: 10px; border: 1px solid #e2e8f0;
        margin-bottom: 1.25rem;
    }
    .meta-item { display: flex; flex-direction: column; }
    .meta-label { font-size: .68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; }
    .meta-value { font-size: .88rem; font-weight: 700; color: #1e293b; }

    /* === BUTTONS === */
    .btn-row { display: flex; gap: .75rem; }
    .btn-save {
        flex: 1; padding: .7rem; border-radius: 10px; border: none;
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        color: white; font-weight: 700; font-size: .9rem;
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        cursor: pointer; transition: all .2s;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(30,58,95,.3); }
    .btn-cancel {
        padding: .7rem 1.5rem; border-radius: 10px;
        border: 1.5px solid #cbd5e1; background: white;
        color: #64748b; font-weight: 700; font-size: .9rem;
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        text-decoration: none; transition: all .2s;
    }
    .btn-cancel:hover { border-color: #94a3b8; color: #1e293b; background: #f8fafc; }

    /* === RESPONSIVE === */
    @media(max-width:768px){
        .page-header { flex-direction: column; align-items: flex-start; padding: 1.25rem 1.5rem; }
        .page-header-title { font-size: 1.25rem; }
        .profile-strip { flex-direction: column; text-align: center; }
        .strip-tags { margin-left: 0; justify-content: center; }
        .form-body { padding: 1.25rem; }
        .field-row { grid-template-columns: 1fr; }
        .btn-row { flex-direction: column; }
        .btn-cancel { justify-content: center; }
        .meta-strip { flex-direction: column; gap: .75rem; }
    }
    @media(max-width:480px){
        .page-header { padding: 1rem 1.2rem; }
        .page-header-title { font-size: 1.1rem; }
        .btn-hdr { padding: .5rem 1rem; font-size: .8rem; }
        .form-body { padding: 1rem; }
        .avatar-md { width: 46px; height: 46px; font-size: 1.1rem; }
    }
</style>

@php
    $rolNombre = $usuario['rol']['nombre'] ?? '';
    $inicial = mb_strtoupper(mb_substr($usuario['nombre'], 0, 1));
    $avClass = match($rolNombre) {
        'Administrador' => 'av-admin',
        'Técnico'       => 'av-tecnico',
        'Normal', 'Usuario Normal' => 'av-normal',
        default         => 'av-otro',
    };
    $rbClass = match($rolNombre) {
        'Administrador' => 'rb-admin',
        'Técnico'       => 'rb-tecnico',
        'Normal', 'Usuario Normal' => 'rb-normal',
        default         => 'rb-otro',
    };
@endphp

{{-- ===== HEADER ===== --}}
<div class="page-header">
    <div>
        <h1 class="page-header-title"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h1>
        <p class="page-header-sub">Modifica la información de la cuenta</p>
    </div>
    <a href="{{ route('usuarios.index') }}" class="btn-hdr btn-hdr-white">
        <i class="bi bi-arrow-left"></i> Volver a Usuarios
    </a>
</div>

{{-- ===== PROFILE STRIP ===== --}}
<div class="profile-strip">
    <div class="avatar-md {{ $avClass }}">{{ $inicial }}</div>
    <div class="strip-info">
        <p class="strip-name">{{ $usuario['nombre'] }} {{ $usuario['apellido'] }}</p>
        <p class="strip-sub"><i class="bi bi-envelope me-1"></i>{{ $usuario['correo'] }}</p>
    </div>
    <div class="strip-tags">
        <span class="role-badge {{ $rbClass }}"><i class="bi bi-shield-check"></i> {{ $rolNombre }}</span>
        @if($usuario['activo'])
            <span class="status-badge sb-active"><i class="bi bi-check-circle-fill"></i> Activo</span>
        @else
            <span class="status-badge sb-inactive"><i class="bi bi-x-circle-fill"></i> Inactivo</span>
        @endif
    </div>
</div>

{{-- ===== FORM CARD ===== --}}
<div class="form-card">
    <div class="form-card-header">
        <i class="bi bi-person-badge"></i> Información del Usuario
    </div>
    <div class="form-body">
        <form action="{{ route('usuarios.update', $usuario['id_usuario']) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Datos personales --}}
            <div class="form-section"><i class="bi bi-person"></i> Datos Personales</div>

            <div class="field-row">
                <div class="field">
                    <label for="nombre" class="field-label">Nombre <span class="req">*</span></label>
                    <input type="text" class="field-input @error('nombre') is-invalid @enderror"
                           id="nombre" name="nombre"
                           value="{{ old('nombre', $usuario['nombre']) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="apellido" class="field-label">Apellido <span class="req">*</span></label>
                    <input type="text" class="field-input @error('apellido') is-invalid @enderror"
                           id="apellido" name="apellido"
                           value="{{ old('apellido', $usuario['apellido']) }}" required>
                    @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="field">
                <label for="correo" class="field-label">Correo Electrónico <span class="req">*</span></label>
                <input type="email" class="field-input @error('correo') is-invalid @enderror"
                       id="correo" name="correo"
                       value="{{ old('correo', $usuario['correo']) }}" required>
                @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Seguridad --}}
            <div class="form-section"><i class="bi bi-shield-lock"></i> Seguridad</div>

            <div class="field-row">
                <div class="field">
                    <label for="password" class="field-label">Nueva Contraseña</label>
                    <div class="pass-wrap">
                        <input type="password" class="field-input @error('password') is-invalid @enderror"
                               id="password" name="password"
                               placeholder="Dejar en blanco para no cambiar">
                        <button type="button" class="pass-toggle-btn" onclick="togglePass('password', this)"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="field-hint">
                        <i class="bi bi-info-circle"></i>
                        Mín. 8 caracteres · Mayúscula · Número · Símbolo (#, @, !, $, %)
                    </div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password_confirmation" class="field-label">Confirmar Contraseña</label>
                    <div class="pass-wrap">
                        <input type="password" class="field-input"
                               id="password_confirmation" name="password_confirmation"
                               placeholder="Repite la nueva contraseña">
                        <button type="button" class="pass-toggle-btn" onclick="togglePass('password_confirmation', this)"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="field-hint">Solo si cambias la contraseña</div>
                </div>
            </div>

            {{-- Rol y estado --}}
            <div class="form-section"><i class="bi bi-gear"></i> Rol y Estado</div>

            <div class="field-row">
                <div class="field">
                    <label for="id_rol" class="field-label">Rol <span class="req">*</span></label>
                    <select class="field-select @error('id_rol') is-invalid @enderror"
                            id="id_rol" name="id_rol" required>
                        @foreach($roles ?? [] as $rol)
                        <option value="{{ $rol['id_rol'] }}"
                                {{ (old('id_rol', $usuario['rol']['id_rol']) == $rol['id_rol']) ? 'selected' : '' }}>
                            {{ $rol['nombre'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label class="field-label">&nbsp;</label>
                    <div class="switch-row">
                        <input class="form-check-input" type="checkbox"
                               id="activo" name="activo" value="1"
                               {{ old('activo', $usuario['activo']) ? 'checked' : '' }}>
                        <label class="switch-label" for="activo">Usuario activo</label>
                    </div>
                </div>
            </div>

            {{-- Meta info --}}
            <div class="meta-strip">
                <div class="meta-item">
                    <span class="meta-label">ID Usuario</span>
                    <span class="meta-value">#{{ $usuario['id_usuario'] }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Creado</span>
                    <span class="meta-value">{{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y H:i') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Última actualización</span>
                    <span class="meta-value">{{ \Carbon\Carbon::parse($usuario['updated_at'])->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="btn-row">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle"></i> Guardar Cambios
                </button>
                <a href="{{ route('usuarios.show', $usuario['id_usuario']) }}" class="btn-cancel">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endsection