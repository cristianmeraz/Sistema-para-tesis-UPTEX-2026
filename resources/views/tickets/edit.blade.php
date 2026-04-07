@extends('layouts.app')

@section('title', 'Editar Ticket')

@section('content')
<style>
    :root { --tl1:#0f766e; --tl2:#0891b2; --tl-grad:linear-gradient(135deg,#0f766e 0%,#0891b2 100%); --tl-glow:rgba(8,145,178,.22); }

    .edit-banner {
        background: var(--tl-grad); border-radius:20px; padding:1.8rem 2.2rem; margin-bottom:2rem;
        position:relative; overflow:hidden; box-shadow:0 8px 32px var(--tl-glow);
        display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;
    }
    .edit-banner::before { content:''; position:absolute; top:-50px; right:-50px; width:190px; height:190px; border-radius:50%; background:rgba(255,255,255,.06); }
    .edit-banner::after  { content:''; position:absolute; bottom:-55px; right:130px; width:130px; height:130px; border-radius:50%; background:rgba(255,255,255,.04); }
    .edit-banner-left { display:flex; align-items:center; gap:1.1rem; position:relative; z-index:1; }
    .edit-banner-icon { width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:1.45rem; color:#fff; flex-shrink:0; }
    .edit-banner-title { color:#fff; font-size:1.45rem; font-weight:700; margin:0; }
    .edit-banner-sub   { color:rgba(255,255,255,.75); font-size:.88rem; margin:.1rem 0 0; }
    .btn-volver-edit { background:rgba(255,255,255,.18); border:1.5px solid rgba(255,255,255,.4); color:#fff; border-radius:10px; padding:.55rem 1.1rem; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:.45rem; transition:background .18s; position:relative; z-index:1; white-space:nowrap; }
    .btn-volver-edit:hover { background:rgba(255,255,255,.28); color:#fff; }

    .edit-card { background:#fff; border-radius:18px; border:1px solid #e8edf5; box-shadow:0 4px 20px rgba(0,0,0,.06); overflow:hidden; }
    .edit-card-header { background:var(--tl-grad); padding:.9rem 1.5rem; display:flex; align-items:center; gap:.6rem; }
    .edit-card-header-title { color:#fff; font-weight:700; font-size:.95rem; }

    .form-label-tl { font-size:.85rem; font-weight:700; color:#374151; margin-bottom:.4rem; }
    .form-control, .form-select {
        border:1.5px solid #e2e8f0; border-radius:10px; font-size:.9rem;
        padding:.7rem 1rem; color:#1e293b; background:#f8fafc; transition:border-color .18s, box-shadow .18s;
    }
    .form-control:focus, .form-select:focus {
        border-color:var(--tl2); box-shadow:0 0 0 3px rgba(8,145,178,.12); background:#fff; outline:none;
    }
    .form-control.is-invalid, .form-select.is-invalid { border-color:#f87171; }
    textarea.form-control { resize:vertical; min-height:130px; }

    .btn-save-tl { background:var(--tl-grad); color:#fff; border:none; border-radius:12px; padding:.85rem 2rem; font-size:1rem; font-weight:700; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; box-shadow:0 4px 15px var(--tl-glow); transition:filter .18s, transform .18s; width:100%; }
    .btn-save-tl:hover { filter:brightness(1.08); transform:translateY(-2px); color:#fff; }
    .btn-cancel-tl { border:1.5px solid #e2e8f0; background:#fff; color:#64748b; border-radius:12px; padding:.82rem 2rem; font-size:.95rem; font-weight:600; width:100%; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; text-decoration:none; transition:border-color .18s; }
    .btn-cancel-tl:hover { border-color:var(--tl2); color:var(--tl1); }

    .info-side-card { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 10px rgba(0,0,0,.05); overflow:hidden; }
    .info-side-header { background:var(--tl-grad); padding:.8rem 1.3rem; }
    .info-side-header span { color:#fff; font-weight:700; font-size:.88rem; }
    .info-side-body { padding:1.2rem 1.3rem; }
    .info-row { display:flex; flex-direction:column; margin-bottom:1rem; }
    .info-row:last-child { margin-bottom:0; }
    .info-row-lbl { font-size:.76rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.25rem; }
    .info-row-val { font-weight:600; color:#1e293b; font-size:.88rem; }

    .chip { display:inline-block; padding:.22rem .65rem; border-radius:20px; font-size:.76rem; font-weight:700; }
    .chip-abierto    { background:#dbeafe; color:#1d4ed8; }
    .chip-en_proceso { background:#e0f2fe; color:#0369a1; }
    .chip-pendiente  { background:#fef9c3; color:#854d0e; }
    .chip-resuelto   { background:#dcfce7; color:#15803d; }
    .chip-cerrado    { background:#f1f5f9; color:#475569; }
</style>

{{-- BANNER --}}
<div class="edit-banner">
    <div class="edit-banner-left">
        <div class="edit-banner-icon"><i class="bi bi-pencil-square"></i></div>
        <div>
            <h1 class="edit-banner-title">Editar Ticket #{{ $ticket['id_ticket'] }}</h1>
            <p class="edit-banner-sub">Modifica la información de tu solicitud</p>
        </div>
    </div>
    <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="btn-volver-edit">
        <i class="bi bi-arrow-left"></i> Ver Ticket
    </a>
</div>

<div class="row g-4">
    {{-- FORMULARIO --}}
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

        <div class="edit-card">
            <div class="edit-card-header">
                <i class="bi bi-pencil-fill" style="color:#fff;"></i>
                <span class="edit-card-header-title">Editar Información del Ticket</span>
            </div>
            <div class="p-4">
                <form action="{{ route('tickets.update', $ticket['id_ticket']) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="titulo" class="form-label-tl">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror"
                               id="titulo" name="titulo"
                               value="{{ old('titulo', $ticket['titulo']) }}" required>
                        @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label-tl">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                  id="descripcion" name="descripcion" rows="5" required>{{ old('descripcion', $ticket['descripcion']) }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="area_id" class="form-label-tl">Área <span class="text-danger">*</span></label>
                            <select class="form-select @error('area_id') is-invalid @enderror"
                                    id="area_id" name="area_id" required>
                                @foreach($areas ?? [] as $area)
                                <option value="{{ $area['id_area'] }}"
                                        {{ (old('area_id', $ticket['area']['id_area']) == $area['id_area']) ? 'selected' : '' }}>
                                    {{ $area['nombre'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('area_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="prioridad_id" class="form-label-tl">Prioridad <span class="text-danger">*</span></label>
                            <select class="form-select @error('prioridad_id') is-invalid @enderror"
                                    id="prioridad_id" name="prioridad_id" required>
                                @foreach($prioridades ?? [] as $prioridad)
                                <option value="{{ $prioridad['id_prioridad'] }}"
                                        {{ (old('prioridad_id', $ticket['prioridad']['id_prioridad']) == $prioridad['id_prioridad']) ? 'selected' : '' }}>
                                    {{ $prioridad['nombre'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('prioridad_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    @if(session('usuario_rol') == 'Administrador' || session('usuario_rol') == 'Técnico')
                    <div class="mb-3">
                        <label for="estado_id" class="form-label-tl">Estado</label>
                        <select class="form-select" id="estado_id" name="estado_id">
                            @foreach($estados ?? [] as $estado)
                            <option value="{{ $estado['id_estado'] }}"
                                    {{ ($ticket['estado']['id_estado'] == $estado['id_estado']) ? 'selected' : '' }}>
                                {{ $estado['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="row g-2 pt-2">
                        <div class="col-12 col-md-8">
                            <button type="submit" class="btn-save-tl">
                                <i class="bi bi-check-circle-fill"></i> Guardar Cambios
                            </button>
                        </div>
                        <div class="col-12 col-md-4">
                            <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="btn-cancel-tl">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- INFO LATERAL --}}
    <div class="col-lg-4">
        <div class="info-side-card">
            <div class="info-side-header">
                <span><i class="bi bi-info-circle me-1"></i>Información del Ticket</span>
            </div>
            <div class="info-side-body">
                <div class="info-row">
                    <span class="info-row-lbl">Folio</span>
                    <span class="info-row-val">#{{ $ticket['id_ticket'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-row-lbl">Fecha de Creación</span>
                    <span class="info-row-val">{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-row-lbl">Estado Actual</span>
                    <span>
                        <span class="chip chip-{{ $ticket['estado']['tipo'] }}">{{ $ticket['estado']['nombre'] }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection