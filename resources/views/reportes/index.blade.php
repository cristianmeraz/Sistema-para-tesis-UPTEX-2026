@extends('layouts.app')

@section('title', 'Estadísticas')
@section('no_header_title', true)

@section('content')
<style>
    /* ══════ BANNER ══════ */
    .rep-banner {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
        border-radius: 18px;
        padding: 2rem 2.2rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(29,78,216,.25);
    }
    .rep-banner::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
    }
    .rep-banner::after {
        content: '';
        position: absolute;
        bottom: -50px; right: 120px;
        width: 130px; height: 130px;
        border-radius: 50%;
        background: rgba(255,255,255,.04);
    }
    .rep-banner-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        background: rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 1.6rem;
        color: #fff;
    }
    .rep-banner-title {
        color: #fff;
        font-size: 1.55rem;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
    }
    .rep-banner-sub {
        color: rgba(255,255,255,.72);
        font-size: .88rem;
        margin: .15rem 0 0;
    }

    /* ══════ CARDS ══════ */
    .rep-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 18px rgba(0,0,0,.05);
        padding: 2rem 1.8rem 1.6rem;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform .22s, box-shadow .22s;
    }
    .rep-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(0,0,0,.10);
    }
    .rep-card-icon-wrap {
        width: 68px; height: 68px;
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
        margin-bottom: 1.3rem;
    }
    .rep-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: .45rem;
    }
    .rep-card-desc {
        font-size: .88rem;
        color: #64748b;
        line-height: 1.55;
        flex-grow: 1;
        margin-bottom: 1.4rem;
    }
    .rep-card-badge {
        display: inline-block;
        font-size: .72rem;
        font-weight: 600;
        padding: .18rem .65rem;
        border-radius: 20px;
        margin-bottom: 1rem;
        letter-spacing: .03em;
    }
    .rep-btn {
        display: flex; align-items: center; justify-content: center; gap: .45rem;
        border: none;
        border-radius: 10px;
        padding: .72rem 1rem;
        font-size: .93rem;
        font-weight: 600;
        width: 100%;
        text-decoration: none;
        transition: filter .18s, transform .18s, box-shadow .18s;
    }
    .rep-btn:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0,0,0,.15);
    }

    /* Variantes de color */
    .rep-icon-blue   { background: #dbeafe; color: #1d4ed8; }
    .rep-badge-blue  { background: #dbeafe; color: #1d4ed8; }
    .rep-btn-blue    { background: linear-gradient(135deg, #1e3a5f, #1d4ed8); color: #fff;
                       box-shadow: 0 4px 14px rgba(29,78,216,.28); }

    .rep-icon-amber  { background: #fef9c3; color: #b45309; }
    .rep-badge-amber { background: #fef3c7; color: #b45309; }
    .rep-btn-amber   { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff;
                       box-shadow: 0 4px 14px rgba(245,158,11,.30); }

    .rep-icon-green  { background: #dcfce7; color: #16a34a; }
    .rep-badge-green { background: #dcfce7; color: #16a34a; }
    .rep-btn-green   { background: linear-gradient(135deg, #15803d, #16a34a); color: #fff;
                       box-shadow: 0 4px 14px rgba(22,163,74,.28); }

    @media (max-width: 768px) {
        .rep-banner { padding: 1.4rem 1.2rem; }
        .rep-banner-title { font-size: 1.2rem; }
        .rep-card { padding: 1.5rem 1.3rem 1.3rem; }
    }
</style>

<div class="container-fluid">

    {{-- ══════ BANNER ══════ --}}
    <div class="rep-banner">
        <div class="rep-banner-icon">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div>
            <h1 class="rep-banner-title">Estadísticas</h1>
            <p class="rep-banner-sub">Genera, analiza y exporta datos del sistema de soporte</p>
        </div>
    </div>

    {{-- ══════ CARDS ══════ --}}
    <div class="row g-4 justify-content-center">

        {{-- Tickets por Fecha --}}
        <div class="col-12 col-md-5">
            <div class="rep-card">
                <div class="rep-card-icon-wrap rep-icon-blue">
                    <i class="bi bi-calendar-range"></i>
                </div>
                <span class="rep-card-badge rep-badge-blue">Filtro temporal</span>
                <div class="rep-card-title">Tickets por Fecha</div>
                <div class="rep-card-desc">
                    Genera un reporte detallado de tickets creados dentro de un rango de fechas personalizado.
                </div>
                <a href="{{ route('reportes.por-fecha') }}" class="rep-btn rep-btn-blue">
                    <i class="bi bi-bar-chart-line"></i> Ver Reporte
                </a>
            </div>
        </div>

        {{-- Rendimiento de Técnicos --}}
        <div class="col-12 col-md-5">
            <div class="rep-card">
                <div class="rep-card-icon-wrap rep-icon-amber">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="rep-card-badge rep-badge-amber">Desempeño</span>
                <div class="rep-card-title">Rendimiento de Técnicos</div>
                <div class="rep-card-desc">
                    Consulta las estadísticas de resolución y desempeño individual de cada técnico de soporte.
                </div>
                <a href="{{ route('reportes.rendimiento') }}" class="rep-btn rep-btn-amber">
                    <i class="bi bi-eye-fill"></i> Ver Reporte
                </a>
            </div>
        </div>

    </div>
</div>
@endsection