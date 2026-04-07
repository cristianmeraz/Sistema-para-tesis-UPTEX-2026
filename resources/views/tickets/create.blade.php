@extends('layouts.app')

@section('title', 'Crear Ticket')

@section('content')
<style>
    :root { --tl1:#0f766e; --tl2:#0891b2; --tl-grad:linear-gradient(135deg,#0f766e 0%,#0891b2 100%); --tl-glow:rgba(8,145,178,.22); }

    .create-banner {
        background: var(--tl-grad);
        border-radius: 20px; padding: 1.8rem 2.2rem; margin-bottom: 2rem;
        position: relative; overflow: hidden;
        box-shadow: 0 8px 32px var(--tl-glow);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    }
    .create-banner::before { content:''; position:absolute; top:-50px; right:-50px; width:190px; height:190px; border-radius:50%; background:rgba(255,255,255,.06); }
    .create-banner::after  { content:''; position:absolute; bottom:-55px; right:130px; width:130px; height:130px; border-radius:50%; background:rgba(255,255,255,.04); }
    .create-banner-icon { width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:1.45rem; color:#fff; flex-shrink:0; }
    .create-banner-title { color:#fff; font-size:1.45rem; font-weight:700; margin:0; position:relative; z-index:1; }
    .create-banner-sub   { color:rgba(255,255,255,.75); font-size:.88rem; margin:.1rem 0 0; position:relative; z-index:1; }
    .create-banner-left  { display:flex; align-items:center; gap:1.1rem; position:relative; z-index:1; }
    .btn-volver-create { background:rgba(255,255,255,.18); border:1.5px solid rgba(255,255,255,.4); color:#fff; border-radius:10px; padding:.55rem 1.1rem; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:.45rem; transition:background .18s; position:relative; z-index:1; white-space:nowrap; }
    .btn-volver-create:hover { background:rgba(255,255,255,.28); color:#fff; }

    .form-card { background:#fff; border-radius:18px; border:1px solid #e8edf5; box-shadow:0 4px 20px rgba(0,0,0,.06); overflow:hidden; }
    .form-card-header { padding:1.1rem 1.7rem; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:.6rem; }
    .form-card-header-icon { width:34px; height:34px; border-radius:10px; background:linear-gradient(135deg,#ccfbf1,#e0f2fe); display:flex; align-items:center; justify-content:center; color:var(--tl1); font-size:1rem; }
    .form-card-header-title { font-weight:700; color:#1e293b; font-size:.95rem; }
    .form-card-body { padding:1.8rem; }

    .form-label-tl { font-size:.85rem; font-weight:700; color:#374151; margin-bottom:.4rem; }
    .form-control, .form-select {
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-size: .9rem; padding: .7rem 1rem; color: #1e293b;
        background: #f8fafc; transition: border-color .18s, box-shadow .18s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--tl2); box-shadow: 0 0 0 3px rgba(8,145,178,.12);
        background: #fff; outline: none;
    }
    .form-control.is-invalid, .form-select.is-invalid { border-color:#f87171; }
    textarea.form-control { resize: vertical; min-height: 140px; }

    .hint-badge { display:inline-flex; align-items:center; gap:.35rem; background:#f0fdfa; border:1px solid #99f6e4; border-radius:8px; padding:.3rem .7rem; font-size:.76rem; color:var(--tl1); font-weight:600; margin-bottom: .6rem; }

    .btn-submit-tl {
        background: var(--tl-grad); color:#fff; border:none; border-radius:12px;
        padding:.85rem 2rem; font-size:1rem; font-weight:700;
        display:inline-flex; align-items:center; gap:.5rem;
        box-shadow: 0 4px 15px var(--tl-glow); transition:filter .18s, transform .18s; width:100%;
        justify-content: center;
    }
    .btn-submit-tl:hover { filter:brightness(1.08); transform:translateY(-2px); color:#fff; }
    .btn-cancel-tl { border:1.5px solid #e2e8f0; background:#fff; color:#64748b; border-radius:12px; padding:.82rem 2rem; font-size:.95rem; font-weight:600; width:100%; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; text-decoration:none; transition:border-color .18s; }
    .btn-cancel-tl:hover { border-color:var(--tl2); color:var(--tl1); }
</style>

{{-- BANNER --}}
<div class="create-banner">
    <div class="create-banner-left">
        <div class="create-banner-icon"><i class="bi bi-plus-circle-fill"></i></div>
        <div>
            <h1 class="create-banner-title">Crear Nuevo Ticket</h1>
            <p class="create-banner-sub">Describe tu problema y nuestro equipo te ayudará</p>
        </div>
    </div>
    <a href="{{ route('dashboard') }}" class="btn-volver-create">
        <i class="bi bi-arrow-left"></i> Volver al Dashboard
    </a>
</div>

{{-- FORMULARIO --}}
<div class="row justify-content-center">
    <div class="col-lg-8">

        @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-3 mb-3" style="background:#fee2e2;">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                <strong>Revisa los siguientes errores:</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon"><i class="bi bi-ticket-perforated-fill"></i></div>
                <span class="form-card-header-title">Detalle del Ticket</span>
            </div>
            <div class="form-card-body">
                <form action="{{ route('tickets.store') }}" method="POST" id="ticketForm">
                    @csrf

                    {{-- TÍTULO --}}
                    <div class="mb-4">
                        <label for="titulo" class="form-label-tl">Título del Problema <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('titulo') is-invalid @enderror"
                               id="titulo" name="titulo"
                               value="{{ old('titulo') }}"
                               placeholder="Ej: No puedo acceder a mi correo institucional"
                               required>
                        @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="mb-4">
                        <label for="descripcion" class="form-label-tl">Descripción Detallada <span class="text-danger">*</span></label>
                        <div class="hint-badge mb-2">
                            <i class="bi bi-lightbulb"></i>
                            Incluye qué estabas haciendo, mensajes de error y cuándo ocurrió
                        </div>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                  id="descripcion" name="descripcion" rows="6"
                                  placeholder="Describe el problema con el mayor detalle posible..." required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ÁREA + PRIORIDAD --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="area_id" class="form-label-tl">Área a la que perteneces <span class="text-danger">*</span></label>
                            <select class="form-select @error('area_id') is-invalid @enderror"
                                    id="area_id" name="area_id" required>
                                <option value="">Selecciona tu área</option>
                                @foreach($areas ?? [] as $area)
                                <option value="{{ $area['id_area'] }}" {{ old('area_id', $userAreaId ?? '') == $area['id_area'] ? 'selected' : '' }}>
                                    {{ $area['nombre'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('area_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            @if($esAdmin ?? false)
                                {{-- ADMIN: puede elegir prioridad al crear --}}
                                <label for="prioridad_id" class="form-label-tl">Prioridad <span class="text-danger">*</span></label>
                                <select class="form-select @error('prioridad_id') is-invalid @enderror"
                                        id="prioridad_id" name="prioridad_id" required>
                                    <option value="">Selecciona prioridad</option>
                                    @foreach($prioridades ?? [] as $prioridad)
                                    <option value="{{ $prioridad['id_prioridad'] ?? $prioridad->id_prioridad }}"
                                        {{ old('prioridad_id') == ($prioridad['id_prioridad'] ?? $prioridad->id_prioridad) ? 'selected' : '' }}>
                                        {{ $prioridad['nombre'] ?? $prioridad->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('prioridad_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @else
                                {{-- USUARIO NORMAL: no elige prioridad, el admin la asigna --}}
                                <label class="form-label-tl">Prioridad</label>
                                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3"
                                     style="background:#f0f9ff; border:2px dashed #93c5fd; min-height:42px;">
                                    <i class="bi bi-clock-history" style="color:#3b82f6; font-size:1rem; flex-shrink:0;"></i>
                                    <div>
                                        <div style="font-size:.78rem; font-weight:700; color:#1d4ed8; line-height:1.2;">
                                            Pendiente de asignación
                                        </div>
                                        <div style="font-size:.7rem; color:#6b7280; line-height:1.1;">
                                            El administrador asignará la prioridad
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="row g-2 pt-2">
                        <div class="col-12 col-md-8">
                            <button type="submit" class="btn-submit-tl">
                                <i class="bi bi-send-fill"></i> Enviar Ticket
                            </button>
                        </div>
                        <div class="col-12 col-md-4">
                            <a href="{{ route('dashboard') }}" class="btn-cancel-tl">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection