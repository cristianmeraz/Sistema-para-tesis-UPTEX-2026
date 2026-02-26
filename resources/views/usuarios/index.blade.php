@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<style>
    body { background: #f1f5f9; }

    /* === HEADER BANNER === */
    .page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        box-shadow: 0 8px 24px rgba(30,58,95,.25);
    }
    .page-header-title { font-size: 1.65rem; font-weight: 800; margin: 0; }
    .page-header-sub { font-size: .9rem; opacity: .8; margin: .25rem 0 0; }
    .dot-live {
        display: inline-block; width: 8px; height: 8px;
        border-radius: 50%; background: #4ade80;
        margin-right: .4rem;
        animation: blink 1.6s infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    .btn-hdr {
        padding: .6rem 1.3rem; border-radius: 8px; font-weight: 700;
        font-size: .88rem; display: inline-flex; align-items: center;
        gap: .4rem; text-decoration: none; transition: all .2s ease;
        border: none; cursor: pointer; white-space: nowrap;
    }
    .btn-hdr-white { background: white; color: #1e3a5f; }
    .btn-hdr-white:hover { background: #dbeafe; color: #1e3a5f; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.15); }
    .btn-hdr-indigo { background: #4f46e5; color: white; }
    .btn-hdr-indigo:hover { background: #4338ca; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79,70,229,.4); }

    /* === KPI CARDS === */
    a.kpi-card { text-decoration: none !important; color: inherit !important; display: block; }
    .kpi-card {
        background: white; border: 1px solid #dbeafe; border-radius: 14px;
        padding: 1.3rem 1.4rem; transition: all .3s cubic-bezier(.4,0,.2,1);
        position: relative; overflow: hidden; height: 100%;
    }
    .kpi-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0;
        height: 3px; background: var(--kpi-color, #2563eb);
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(30,58,95,.13); border-color: var(--kpi-color, #2563eb); }
    .kpi-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        background: color-mix(in srgb, var(--kpi-color, #2563eb) 12%, white);
        color: var(--kpi-color, #2563eb); margin-bottom: .6rem;
    }
    .kpi-value { font-size: 2.1rem; font-weight: 800; color: var(--kpi-color, #2563eb); line-height: 1; margin-bottom: .2rem; }
    .kpi-label { font-size: .72rem; font-weight: 700; color: #94a3b8; letter-spacing: .05em; text-transform: none; }
    .kpi-sub   { font-size: .82rem; color: #64748b; margin-top: .2rem; }

    /* === SECTION HEADER === */
    .section-header {
        display: flex; align-items: center; gap: .6rem;
        font-size: .95rem; font-weight: 700; color: #1e3a5f;
        padding-bottom: .6rem; border-bottom: 2px solid #dbeafe;
        margin-bottom: 1rem;
    }

    /* === FILTER BOX === */
    .filter-box {
        background: white; border: 1px solid #dbeafe;
        border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1rem;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 1rem; align-items: flex-end;
    }
    .filter-label { font-weight: 700; color: #1e3a5f; font-size: .82rem; margin-bottom: .35rem; display: block; text-transform: uppercase; letter-spacing: .04em; }
    .filter-select {
        width: 100%; border: 1.5px solid #dbeafe; border-radius: 8px;
        padding: .6rem 1rem; font-size: .92rem; color: #1e3a5f;
        background: white; transition: border-color .2s;
    }
    .filter-select:focus { border-color: #1d4ed8; outline: none; box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
    .btn-filter {
        padding: .65rem 1.4rem; background: #1d4ed8; color: white;
        border: none; border-radius: 8px; font-weight: 700; font-size: .9rem;
        cursor: pointer; display: inline-flex; align-items: center; gap: .4rem;
        transition: background .2s; white-space: nowrap;
    }
    .btn-filter:hover { background: #1e3a5f; }
    .btn-clear-link {
        color: #ef4444; font-size: .85rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: .3rem;
    }
    .btn-clear-link:hover { color: #dc2626; }

    /* === FILTER BANNER === */
    .filter-banner {
        background: #eff6ff; border: 1.5px solid #bfdbfe;
        border-radius: 10px; padding: .7rem 1.2rem; margin-bottom: 1rem;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: .5rem;
    }
    .filter-tag { background: #2563eb; color: white; padding: .25rem .75rem; border-radius: 20px; font-size: .8rem; font-weight: 700; }

    /* === TABLE === */
    .table-card { background: white; border: 1px solid #dbeafe; border-radius: 14px; overflow: hidden; }
    .table-card-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        color: white; padding: 1rem 1.5rem;
        display: flex; align-items: center; gap: .6rem;
        font-weight: 700; font-size: .95rem;
    }
    .tbl { width: 100%; margin: 0; border-collapse: collapse; }
    .tbl thead th {
        background: #f8fafc; padding: .85rem 1rem;
        font-size: .72rem; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .05em;
        border-bottom: 2px solid #dbeafe; white-space: nowrap;
    }
    .tbl tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
    .tbl tbody tr:hover { background: #f8fafc; }
    .tbl tbody td { padding: .8rem 1rem; font-size: .9rem; vertical-align: middle; }
    .tbl tbody tr:last-child { border-bottom: none; }

    /* === AVATAR === */
    .avatar {
        width: 38px; height: 38px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 800; color: white; font-size: .85rem; flex-shrink: 0;
    }
    .av-admin   { background: linear-gradient(135deg, #1e3a5f, #1d4ed8); }
    .av-tecnico { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
    .av-normal  { background: linear-gradient(135deg, #0891b2, #06b6d4); }
    .av-otro    { background: linear-gradient(135deg, #475569, #64748b); }

    /* === ROLE BADGES === */
    .role-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .35rem .85rem; border-radius: 20px;
        font-size: .78rem; font-weight: 700; white-space: nowrap;
    }
    .rb-admin   { background: #1e3a5f; color: white; }
    .rb-tecnico { background: #4f46e5; color: white; }
    .rb-normal  { background: #0891b2; color: white; }
    .rb-otro    { background: #475569; color: white; }

    /* === STATUS BADGE === */
    .status-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .8rem; border-radius: 8px; font-size: .8rem; font-weight: 700;
    }
    .sb-active   { background: #d1fae5; color: #065f46; }
    .sb-inactive { background: #fee2e2; color: #991b1b; }

    /* === USER ID === */
    .user-id { font-weight: 800; color: #1d4ed8; }

    /* === ACTION BUTTONS (NO MODIFICAR) === */
    .action-btn {
        border: none; border-radius: 6px; font-weight: 600;
        padding: 0.4rem 0.8rem; font-size: 0.75rem;
        transition: all 0.3s ease; cursor: pointer;
    }
    .btn-view-user   { background-color: #E3F2FD; color: #1565C0; }
    .btn-view-user:hover   { background-color: #1565C0; color: white; box-shadow: 0 2px 8px rgba(21,101,192,.3); }
    .btn-edit-user   { background-color: #FFF3E0; color: #E65100; }
    .btn-edit-user:hover   { background-color: #E65100; color: white; box-shadow: 0 2px 8px rgba(230,81,0,.3); }
    .btn-toggle-user { background-color: #F3E5F5; color: #6A1B9A; }
    .btn-toggle-user:hover { background-color: #6A1B9A; color: white; box-shadow: 0 2px 8px rgba(106,27,154,.3); }
    .btn-delete-user { background-color: #FFEBEE; color: #C62828; }
    .btn-delete-user:hover { background-color: #C62828; color: white; box-shadow: 0 2px 8px rgba(198,40,40,.3); }

    /* === EMPTY STATE === */
    .empty-state { text-align: center; padding: 3.5rem 2rem; }
    .empty-state i { font-size: 3.5rem; color: #cbd5e1; display: block; margin-bottom: .75rem; }

    /* === PAGINATION === */
    .pag-wrap { display: flex; justify-content: center; flex-wrap: wrap; gap: .4rem; margin-top: 1.5rem; }
    .pag-wrap a, .pag-wrap span.pag-num {
        padding: .45rem .8rem; border-radius: 6px; font-size: .85rem; text-decoration: none; transition: all .2s;
    }
    .pag-wrap a { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .pag-wrap a:hover { background: #1d4ed8; color: white; }
    .pag-wrap span.pag-active { background: #1e3a5f; color: white; border-radius: 6px; padding: .45rem .8rem; font-size: .85rem; }
    .pag-wrap span.pag-disabled { opacity: .4; padding: .45rem .8rem; font-size: .85rem; }

    @media(max-width:768px){
        .page-header { flex-direction: column; align-items: flex-start; }
        .filter-grid { grid-template-columns: 1fr; }
        .kpi-card { padding: 1rem; }
    }
</style>

@php
    /* Etiquetas de filtros activos */
    $filterLabels = [];
    if (request('id_rol')) {
        $rn = $roles->firstWhere('id_rol', (int) request('id_rol'));
        if ($rn) $filterLabels[] = 'Rol: ' . $rn->nombre;
    }
    if (request('activo') !== null && request('activo') !== '') {
        $filterLabels[] = request('activo') == '1' ? 'Estado: Activos' : 'Estado: Inactivos';
    }
@endphp

{{-- ===== HEADER ===== --}}
<div class="page-header">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-people-fill me-2"></i>Gestión de Usuarios
        </h1>
        <p class="page-header-sub">
            <span class="dot-live"></span>
            {{ $usuarios->total() }} usuario(s) en el sistema
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.usuarios.create') }}" class="btn-hdr btn-hdr-white">
            <i class="bi bi-person-plus-fill"></i> Crear Usuario
        </a>
        <a href="{{ route('admin.tecnicos.create') }}" class="btn-hdr btn-hdr-white">
            <i class="bi bi-person-badge-fill"></i> Crear Técnico
        </a>
    </div>
</div>

{{-- ===== KPI CARDS ===== --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card" style="--kpi-color:#1e3a5f;">
            <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-label">Total Usuarios</div>
            <div class="kpi-sub">{{ $stats['activos'] }} activos actualmente</div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="{{ route('usuarios.index', ['id_rol' => $rolIds['tecnico'] ?? '']) }}" class="kpi-card" style="--kpi-color:#4f46e5;">
            <div class="kpi-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="kpi-value">{{ $stats['tecnicos'] }}</div>
            <div class="kpi-label">Técnicos</div>
            <div class="kpi-sub">Equipo de soporte activo</div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="{{ route('usuarios.index', ['id_rol' => $rolIds['admin'] ?? '']) }}" class="kpi-card" style="--kpi-color:#1d4ed8;">
            <div class="kpi-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="kpi-value">{{ $stats['admins'] }}</div>
            <div class="kpi-label">Administradores</div>
            <div class="kpi-sub">Control total del sistema</div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="{{ route('usuarios.index', ['id_rol' => $rolIds['normal'] ?? '']) }}" class="kpi-card" style="--kpi-color:#0891b2;">
            <div class="kpi-icon"><i class="bi bi-person-fill"></i></div>
            <div class="kpi-value">{{ $stats['normales'] }}</div>
            <div class="kpi-label">Usuarios Normales</div>
            <div class="kpi-sub">Reportan tickets al sistema</div>
        </a>
    </div>
</div>

{{-- ===== FILTROS ===== --}}
<div class="section-header">
    <i class="bi bi-funnel-fill" style="color:#2563eb;"></i>
    Filtros de Búsqueda
</div>

@if(count($filterLabels) > 0)
<div class="filter-banner">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <i class="bi bi-funnel-fill" style="color:#2563eb;"></i>
        <span style="font-weight:700; color:#1e3a5f; font-size:.88rem;">Filtrando por:</span>
        @foreach($filterLabels as $lbl)
            <span class="filter-tag">{{ $lbl }}</span>
        @endforeach
    </div>
    <a href="{{ route('usuarios.index') }}" class="btn-clear-link">
        <i class="bi bi-x-circle-fill"></i> Quitar filtros
    </a>
</div>
@endif

<div class="filter-box mb-4">
    <form method="GET" action="{{ route('usuarios.index') }}">
        <div class="filter-grid">
            <div>
                <label class="filter-label">Rol</label>
                <select name="id_rol" class="filter-select">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $rol)
                    <option value="{{ $rol->id_rol }}" {{ request('id_rol') == $rol->id_rol ? 'selected' : '' }}>
                        @if($rol->nombre === 'Administrador')
                            <i class="bi bi-shield-lock-fill"></i>
                        @elseif(str_contains($rol->nombre, 'cnico'))
                            <i class="bi bi-person-badge-fill"></i>
                        @endif
                        {{ $rol->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="filter-label">Estado</label>
                <select name="activo" class="filter-select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="d-flex gap-2 align-items-end">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                @if(count($filterLabels) > 0)
                <a href="{{ route('usuarios.index') }}" class="btn-clear-link" style="white-space:nowrap;">
                    <i class="bi bi-x-lg"></i> Limpiar
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- ===== TABLA ===== --}}
<div class="table-card">
    <div class="table-card-header">
        <i class="bi bi-table"></i>
        {{ $usuarios->count() }} usuario(s) mostrado(s)
        @if($usuarios->total() != $usuarios->count())
            <span style="opacity:.7; font-size:.82rem; font-weight:400;">de {{ $usuarios->total() }} total</span>
        @endif
    </div>

    @if($usuarios->count() > 0)
    <div class="table-responsive">
        <table class="tbl">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                @php
                    $rolNombre = $usuario->rol->nombre ?? '';
                    $esAdmin   = $rolNombre === 'Administrador';
                    $esTec     = str_contains($rolNombre, 'cnico');
                    $avatarCls = $esAdmin ? 'av-admin' : ($esTec ? 'av-tecnico' : 'av-normal');
                    $badgeCls  = $esAdmin ? 'rb-admin' : ($esTec ? 'rb-tecnico' : 'rb-normal');
                    $badgeIcon = $esAdmin ? 'bi-shield-lock-fill' : ($esTec ? 'bi-person-badge-fill' : 'bi-person-fill');
                @endphp
                <tr>
                    <td><span class="user-id">#{{ $usuario->id_usuario }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar {{ $avatarCls }}">
                                {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                            </div>
                            <span class="fw-600" style="font-weight:600; color:#1e3a5f;">
                                {{ $usuario->nombre }} {{ $usuario->apellido }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <a href="mailto:{{ $usuario->correo }}" style="color:#1d4ed8; text-decoration:none; font-size:.88rem;">
                            <i class="bi bi-envelope-fill me-1" style="opacity:.6;"></i>{{ $usuario->correo }}
                        </a>
                    </td>
                    <td>
                        <span class="role-badge {{ $badgeCls }}">
                            <i class="bi {{ $badgeIcon }}"></i>
                            {{ $rolNombre ?: 'Sin rol' }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge {{ $usuario->activo ? 'sb-active' : 'sb-inactive' }}">
                            <i class="bi {{ $usuario->activo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td style="color:#94a3b8; font-size:.85rem;">
                        {{ $usuario->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-nowrap">
                            <a href="{{ route('usuarios.show', $usuario->id_usuario) }}"
                               class="action-btn btn-view-user" title="Ver">
                                <i class="bi bi-eye-fill me-1"></i>Ver
                            </a>
                            <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}"
                               class="action-btn btn-edit-user" title="Editar">
                                <i class="bi bi-pencil-fill me-1"></i>Editar
                            </a>
                            <form action="{{ route('usuarios.toggle-activo', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="action-btn btn-toggle-user"
                                        title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}">
                                    <i class="bi {{ $usuario->activo ? 'bi-power' : 'bi-arrow-repeat' }} me-1"></i>
                                    {{ $usuario->activo ? 'Desac' : 'Activ' }}
                                </button>
                            </form>
                            @if(session('usuario_id') != $usuario->id_usuario)
                            <form action="{{ route('usuarios.destroy', $usuario->id_usuario) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('¿Eliminar a ' + '{{ addslashes($usuario->nombre) }} {{ addslashes($usuario->apellido) }}' + '?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-delete-user" title="Eliminar">
                                    <i class="bi bi-trash-fill me-1"></i>Elim.
                                </button>
                            </form>
                            @else
                            <button type="button" class="action-btn" disabled
                                    style="opacity:.4; cursor:not-allowed; background:#f1f5f9; color:#64748b;"
                                    title="Tu propia cuenta">
                                <i class="bi bi-lock-fill me-1"></i>Bloq.
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="bi bi-person-x"></i>
        <p style="font-weight:700; color:#64748b;">No hay usuarios que mostrar</p>
        <p style="color:#94a3b8; font-size:.88rem;">Cambia los filtros o crea un nuevo usuario</p>
    </div>
    @endif
</div>

{{-- ===== PAGINACIÓN ===== --}}
@if($usuarios->hasPages())
<div class="pag-wrap">
    @if($usuarios->onFirstPage())
        <span class="pag-disabled">← Anterior</span>
    @else
        <a href="{{ $usuarios->previousPageUrl() }}">← Anterior</a>
    @endif

    @for($p = 1; $p <= $usuarios->lastPage(); $p++)
        @if($p == $usuarios->currentPage())
            <span class="pag-active">{{ $p }}</span>
        @else
            <a href="{{ $usuarios->url($p) }}">{{ $p }}</a>
        @endif
    @endfor

    @if($usuarios->hasMorePages())
        <a href="{{ $usuarios->nextPageUrl() }}">Siguiente →</a>
    @else
        <span class="pag-disabled">Siguiente →</span>
    @endif
</div>
@endif

@endsection
