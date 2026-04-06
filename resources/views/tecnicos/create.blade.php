@extends('layouts.app')

@section('title', 'Registrar Técnico - UPTEX')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4 mt-2 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>Nuevo Personal Técnico</h2>
            <p class="text-muted small">Registra un nuevo técnico en el sistema de soporte.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        <strong>Corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm border-top border-4 border-primary">
                <div class="card-body p-4">
                    <form action="{{ route('admin.tecnicos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_rol" value="2">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Nombre(s):</label>
                                <input type="text" name="nombre" class="form-control bg-light border-0 py-2 @error('nombre') is-invalid @enderror" required placeholder="Ej. Juan" value="{{ old('nombre') }}">
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Apellido(s):</label>
                                <input type="text" name="apellido" class="form-control bg-light border-0 py-2 @error('apellido') is-invalid @enderror" required placeholder="Ej. García" value="{{ old('apellido') }}">
                                @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted text-uppercase">Correo Institucional:</label>
                                <input type="email" name="correo" class="form-control bg-light border-0 py-2 @error('correo') is-invalid @enderror" required placeholder="tecnico@uptex.edu.mx" value="{{ old('correo') }}">
                                @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Contraseña:</label>
                                <input type="password" name="password" class="form-control bg-light border-0 py-2 @error('password') is-invalid @enderror" required placeholder="Mínimo 8 caracteres">
                                <small class="text-muted mt-1 d-block">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Mín. 8 caracteres &middot; Una mayúscula &middot; Un número &middot; Un símbolo (#, @, !, $, %)
                                </small>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Confirmar Contraseña:</label>
                                <input type="password" name="password_confirmation" class="form-control bg-light border-0 py-2" required placeholder="Repite la contraseña">
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                <i class="bi bi-person-check me-1"></i> Registrar Técnico
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection