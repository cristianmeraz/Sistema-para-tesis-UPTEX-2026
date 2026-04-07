@extends('layouts.app')

@section('title', 'Registrar Técnico - UPTEX')

@section('content')
<style>
    body { background: #f1f5f9; }
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

    /* Alert */
    .alert-styled {
        background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 10px;
        padding: 1rem 1.25rem; margin-bottom: 1.5rem; max-width: 780px; margin-left: auto; margin-right: auto;
        color: #991b1b; font-size: .88rem;
    }
    .alert-styled ul { margin: .25rem 0 0; padding-left: 1.2rem; }
    .alert-styled li { margin-bottom: .15rem; }

    .form-card {
        background: white; border: 1px solid #dbeafe; border-radius: 14px;
        overflow: hidden; max-width: 780px; margin: 0 auto;
        box-shadow: 0 2px 8px rgba(30,58,95,.06);
    }
    .form-card-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white; padding: .9rem 1.5rem;
        display: flex; align-items: center; gap: .5rem;
        font-weight: 700; font-size: .9rem;
    }
    .form-body { padding: 1.5rem; }
    .form-section {
        font-size: .75rem; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .05em;
        padding-bottom: .5rem; border-bottom: 2px solid #dbeafe;
        margin: 1.5rem 0 1rem; display: flex; align-items: center; gap: .4rem;
    }
    .form-section:first-child { margin-top: 0; }
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
    .field-input:focus { border-color: #4f46e5; outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,.1); background: white; }
    .field-input.is-invalid { border-color: #ef4444; }
    .field-hint { font-size: .75rem; color: #94a3b8; margin-top: .3rem; display: flex; align-items: flex-start; gap: .3rem; }
    .invalid-feedback { font-size: .78rem; }
    .pass-wrap { position: relative; }
    .pass-wrap .field-input { padding-right: 2.8rem; }
    .pass-toggle-btn {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: #94a3b8; cursor: pointer;
        padding: .25rem; font-size: 1.1rem; line-height: 1;
    }
    .pass-toggle-btn:hover { color: #4f46e5; }
    .btn-row { display: flex; gap: .75rem; }
    .btn-save {
        flex: 1; padding: .7rem; border-radius: 10px; border: none;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white; font-weight: 700; font-size: .9rem;
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        cursor: pointer; transition: all .2s;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(79,70,229,.3); }
    .btn-cancel {
        padding: .7rem 1.5rem; border-radius: 10px;
        border: 1.5px solid #cbd5e1; background: white;
        color: #64748b; font-weight: 700; font-size: .9rem;
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        text-decoration: none; transition: all .2s;
    }
    .btn-cancel:hover { border-color: #94a3b8; color: #1e293b; background: #f8fafc; }

    @media(max-width:768px){
        .page-header { flex-direction: column; align-items: flex-start; padding: 1.25rem 1.5rem; }
        .page-header-title { font-size: 1.25rem; }
        .form-body { padding: 1.25rem; }
        .field-row { grid-template-columns: 1fr; }
        .btn-row { flex-direction: column; }
        .btn-cancel { justify-content: center; }
    }
    @media(max-width:480px){
        .page-header { padding: 1rem 1.2rem; }
        .page-header-title { font-size: 1.1rem; }
        .btn-hdr { padding: .5rem 1rem; font-size: .8rem; }
        .form-body { padding: 1rem; }
    }
</style>

{{-- ===== HEADER ===== --}}
<div class="page-header">
    <div>
        <h1 class="page-header-title"><i class="bi bi-person-badge me-2"></i>Nuevo Personal Técnico</h1>
        <p class="page-header-sub">Registra un nuevo técnico en el sistema de soporte</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn-hdr btn-hdr-white">
        <i class="bi bi-arrow-left"></i> Volver al Panel
    </a>
</div>

@if($errors->any())
<div class="alert-styled">
    <strong><i class="bi bi-exclamation-circle-fill me-1"></i> Corrige los siguientes errores:</strong>
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ===== FORM ===== --}}
<div class="form-card">
    <div class="form-card-header">
        <i class="bi bi-wrench-adjustable"></i> Datos del Técnico
    </div>
    <div class="form-body">
        <form action="{{ route('admin.tecnicos.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_rol" value="2">

            <div class="form-section"><i class="bi bi-person"></i> Datos Personales</div>

            <div class="field-row">
                <div class="field">
                    <label for="nombre" class="field-label">Nombre(s) <span class="req">*</span></label>
                    <input type="text" class="field-input @error('nombre') is-invalid @enderror"
                           id="nombre" name="nombre" required placeholder="Ej. Juan" value="{{ old('nombre') }}">
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="apellido" class="field-label">Apellido(s) <span class="req">*</span></label>
                    <input type="text" class="field-input @error('apellido') is-invalid @enderror"
                           id="apellido" name="apellido" required placeholder="Ej. García" value="{{ old('apellido') }}">
                    @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="field">
                <label for="correo" class="field-label">Correo Institucional <span class="req">*</span></label>
                <input type="email" class="field-input @error('correo') is-invalid @enderror"
                       id="correo" name="correo" required placeholder="tecnico@uptex.edu.mx" value="{{ old('correo') }}">
                @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-section"><i class="bi bi-shield-lock"></i> Seguridad</div>

            <div class="field-row">
                <div class="field">
                    <label for="password" class="field-label">Contraseña <span class="req">*</span></label>
                    <div class="pass-wrap">
                        <input type="password" class="field-input @error('password') is-invalid @enderror"
                               id="password" name="password" required placeholder="Mínimo 8 caracteres">
                        <button type="button" class="pass-toggle-btn" onclick="togglePass('password',this)"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="field-hint">
                        <i class="bi bi-info-circle"></i>
                        Mín. 8 caracteres · Mayúscula · Número · Símbolo (#, @, !, $, %)
                    </div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password_confirmation" class="field-label">Confirmar Contraseña <span class="req">*</span></label>
                    <div class="pass-wrap">
                        <input type="password" class="field-input"
                               id="password_confirmation" name="password_confirmation" required placeholder="Repite la contraseña">
                        <button type="button" class="pass-toggle-btn" onclick="togglePass('password_confirmation',this)"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-save">
                    <i class="bi bi-person-check"></i> Registrar Técnico
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn-cancel">
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