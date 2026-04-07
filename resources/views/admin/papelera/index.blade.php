@extends('layouts.app')

@section('title', 'Papelera de Tickets')

@section('content')
<style>
    /* ══════ BANNER ══════ */
    .pap-banner {
        background: linear-gradient(135deg, #374151 0%, #6b7280 100%);
        border-radius: 18px;
        padding: 1.4rem 2rem;
        margin-bottom: 1.8rem;
        display: flex; align-items: center; gap: 1.2rem;
        position: relative; overflow: hidden;
        box-shadow: 0 8px 30px rgba(55,65,81,.28);
    }
    .pap-banner::before { content:''; position:absolute; top:-40px; right:-40px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.06); }
    .pap-banner-logo { width:48px; height:48px; background:rgba(255,255,255,.92); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; padding:6px; }
    .pap-banner-logo img { width:100%; height:100%; object-fit:contain; }
    .pap-banner-title { color:#fff; font-size:1.3rem; font-weight:700; line-height:1.2; margin:0; }
    .pap-banner-sub { color:rgba(255,255,255,.72); font-size:.82rem; margin:.1rem 0 0; }

    /* ══════ AVISO INFO ══════ */
    .pap-info-box {
        background: #fefce8; border: 1px solid #fde68a; border-radius: 14px;
        padding: .9rem 1.3rem; margin-bottom: 1.4rem;
        display: flex; align-items: flex-start; gap: .8rem;
    }
    .pap-info-box i { color: #b45309; font-size: 1.25rem; flex-shrink: 0; margin-top: 1px; }
    .pap-info-box p { margin: 0; font-size: .84rem; color: #78350f; line-height: 1.5; }

    /* ══════ TABLA ══════ */
    .pap-table-wrap { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,0,0,.04); overflow:hidden; margin-bottom:1.4rem; }
    .pap-table-header { background: linear-gradient(135deg, #374151, #6b7280); padding:.9rem 1.4rem; display:flex; align-items:center; justify-content:space-between; }
    .pap-table-header span { color:#fff; font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
    .pap-table-wrap table { margin:0; font-size:.85rem; }
    .pap-table-wrap thead th { background:#f8f9fa; color:#374151; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; padding:.65rem 1rem; border-bottom:2px solid #e5e7eb; white-space:nowrap; }
    .pap-table-wrap tbody td { padding:.7rem 1rem; color:#334155; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    .pap-table-wrap tbody tr:last-child td { border-bottom:none; }
    .pap-table-wrap tbody tr:hover td { background:#f9fafb; }

    /* ══════ CHIPS ══════ */
    .chip { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; padding:.22rem .6rem; border-radius:20px; white-space:nowrap; }
    .chip-rojo   { background:#fee2e2; color:#b91c1c; }
    .chip-naranja{ background:#fef3c7; color:#b45309; }
    .chip-verde  { background:#dcfce7; color:#15803d; }
    .chip-gris   { background:#f1f5f9; color:#475569; }

    /* ══════ BOTÓN VOLVER / RESTAURAR ══════ */
    .btn-volver { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:.62rem 1.2rem; font-weight:600; font-size:.9rem; display:inline-flex; align-items:center; gap:.5rem; transition:background .18s; text-decoration:none; }
    .btn-volver:hover { background:#e2e8f0; color:#1e293b; }
    .btn-restaurar { background: linear-gradient(135deg,#1e3a5f,#1d4ed8); color:#fff; border:none; border-radius:8px; padding:.38rem .9rem; font-size:.78rem; font-weight:600; display:inline-flex; align-items:center; gap:.35rem; box-shadow: 0 2px 8px rgba(29,78,216,.25); transition:filter .18s; }
    .btn-restaurar:hover { filter:brightness(1.15); color:#fff; }

    /* ══════ CUENTA REGRESIVA ══════ */
    .dias-chip { display:inline-flex; align-items:center; gap:.3rem; font-size:.76rem; font-weight:700; padding:.25rem .65rem; border-radius:20px; }
    .dias-critico { background:#fee2e2; color:#b91c1c; }
    .dias-warning { background:#fef3c7; color:#b45309; }
    .dias-ok      { background:#dcfce7; color:#15803d; }

    /* ══════ EMPTY STATE ══════ */
    .empty-pap { text-align:center; padding:4rem 2rem; color:#94a3b8; }
    .empty-pap i { font-size:3.5rem; display:block; margin-bottom:.8rem; }
    .empty-pap p { font-size:.95rem; margin:0; }
</style>

{{-- FLASH MESSAGES --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert" style="border-radius:12px;">
    <i class="bi bi-check-circle-fill"></i>
    {{ session('success') }}
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- BANNER --}}
<div class="pap-banner">
    <div class="pap-banner-logo">
        <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX">
    </div>
    <div class="flex-grow-1">
        <h1 class="pap-banner-title"><i class="bi bi-trash3 me-2"></i>Papelera de Tickets</h1>
        <p class="pap-banner-sub">
            Tickets archivados automáticamente — se eliminan de forma permanente a los 5 días de ser archivados
            @if($rol === 'Técnico')
            &nbsp;·&nbsp; Mostrando solo tus tickets
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn-volver">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>
</div>

{{-- AVISO informativo --}}
<div class="pap-info-box">
    <i class="bi bi-info-circle-fill"></i>
    <p>
        <strong>¿Cómo funciona la papelera?</strong><br>
        Los tickets en estado <em>Resuelto / Cerrado / Cancelado</em> se archivan automáticamente
        <strong>4 meses después de su fecha de cierre</strong>. Una vez en la papelera, tienes
        <strong>5 días</strong> para restaurarlos antes de que se eliminen de forma permanente
        (incluidos sus comentarios y encuestas de satisfacción).
    </p>
</div>

{{-- TABLA --}}
<div class="pap-table-wrap">
    <div class="pap-table-header">
        <span><i class="bi bi-trash3-fill me-2"></i>Tickets en Papelera</span>
        <span style="color:rgba(255,255,255,.7); font-size:.75rem;">
            {{ $tickets->count() }} {{ $tickets->count() === 1 ? 'ticket' : 'tickets' }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Área</th>
                    <th>Técnico</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Fecha Cierre</th>
                    <th class="text-center">Archivado</th>
                    <th class="text-center">Elimina en</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                @php
                    $dias = $t->dias_restantes;
                    $diasClass = $dias <= 1 ? 'dias-critico' : ($dias <= 3 ? 'dias-warning' : 'dias-ok');
                    $diasLabel = $dias === 0 ? 'hoy' : ($dias === 1 ? '1 día' : "{$dias} días");
                @endphp
                <tr>
                    <td class="fw-bold" style="color:#6b7280;">{{ $t->id_ticket }}</td>
                    <td>
                        <span class="fw-semibold" style="color:#1e293b;">{{ Str::limit($t->titulo, 45) }}</span>
                    </td>
                    <td>
                        <span class="chip chip-gris">{{ $t->area->nombre ?? '—' }}</span>
                    </td>
                    <td style="font-size:.82rem; color:#475569;">
                        {{ $t->tecnicoAsignado ? $t->tecnicoAsignado->nombre . ' ' . $t->tecnicoAsignado->apellido : 'Sin asignar' }}
                    </td>
                    <td class="text-center">
                        @php
                            $tipo = $t->estado->tipo ?? 'abierto';
                            $chipClass = match($tipo) {
                                'resuelto','cerrado'   => 'chip-verde',
                                'en_proceso'           => 'chip-naranja',
                                'cancelado'            => 'chip-rojo',
                                default                => 'chip-gris',
                            };
                        @endphp
                        <span class="chip {{ $chipClass }}">{{ $t->estado->nombre ?? '—' }}</span>
                    </td>
                    <td class="text-center" style="font-size:.8rem; color:#64748b; white-space:nowrap;">
                        {{ $t->fecha_cierre ? $t->fecha_cierre->format('d/m/Y') : '—' }}
                    </td>
                    <td class="text-center" style="font-size:.8rem; color:#64748b; white-space:nowrap;">
                        {{ $t->archivado_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="text-center">
                        <span class="dias-chip {{ $diasClass }}">
                            <i class="bi bi-clock{{ $dias <= 1 ? '-fill' : '' }}"></i>
                            {{ $diasLabel }}
                        </span>
                    </td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('papelera.restaurar', $t->id_ticket) }}"
                              onsubmit="return confirm('¿Restaurar el ticket #{{ $t->id_ticket }}? Se moverá de vuelta a la lista activa.')">
                            @csrf
                            <button type="submit" class="btn-restaurar">
                                <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-pap">
                            <i class="bi bi-trash3"></i>
                            <p>La papelera está vacía</p>
                            <small style="color:#cbd5e1;">Los tickets archivados aparecerán aquí antes de eliminarse permanentemente</small>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
