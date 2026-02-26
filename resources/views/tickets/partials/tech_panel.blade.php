{{-- PANEL TÉCNICO INTEGRADO --}}
{{-- Vista parcial para mostrar estadísticas y acciones del equipo técnico --}}

<style>
    .tech-panel {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(30, 58, 138, 0.2);
        position: relative;
        overflow: hidden;
    }

    .tech-panel::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(50%, -50%);
    }

    .tech-panel-content {
        position: relative;
        z-index: 2;
    }

    .tech-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .tech-panel-title {
        font-size: 1.6rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .tech-panel-title i {
        font-size: 2rem;
    }

    .tech-status-badge {
        background: rgba(16, 185, 129, 0.2);
        border: 2px solid #10b981;
        color: #10b981;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tech-status-badge .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #10b981;
        animation: pulse-status 2s infinite;
    }

    @keyframes pulse-status {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .tech-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .tech-stat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .tech-stat-card:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-4px);
    }

    .tech-stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.5rem;
    }

    .tech-stat-value {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #10b981 0%, #6ee7b7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .tech-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }

    .tech-action-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        text-decoration: none;
    }

    .tech-action-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        color: white;
    }

    .tech-action-btn.primary {
        background: linear-gradient(135deg, #10b981 0%, #6ee7b7 100%);
        border-color: transparent;
        color: #1e3a8a;
    }

    .tech-action-btn.primary:hover {
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    .tech-alerts {
        background: rgba(239, 68, 68, 0.1);
        border-left: 4px solid #ef4444;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1.5rem;
    }

    .tech-alerts-title {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
        color: #fca5a5;
    }

    .tech-alert-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .tech-alert-item i {
        flex-shrink: 0;
        margin-top: 0.2rem;
        color: #fca5a5;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .tech-panel {
            padding: 1.5rem;
        }

        .tech-panel-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .tech-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .tech-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="tech-panel">
    <div class="tech-panel-content">
        <!-- HEADER -->
        <div class="tech-panel-header">
            <div class="tech-panel-title">
                <i class="bi bi-tools"></i>
                Panel Técnico Integrado
            </div>
            <div class="tech-status-badge">
                <span class="status-indicator"></span>
                Sistema Operativo
            </div>
        </div>

        <!-- ESTADÍSTICAS -->
        <div class="tech-stats-grid">
            <div class="tech-stat-card">
                <div class="tech-stat-label">Mis Tickets</div>
                <div class="tech-stat-value">{{ $tecnico_stats['mis_tickets'] ?? 0 }}</div>
            </div>

            <div class="tech-stat-card">
                <div class="tech-stat-label">En Proceso</div>
                <div class="tech-stat-value">{{ $tecnico_stats['en_proceso'] ?? 0 }}</div>
            </div>

            <div class="tech-stat-card">
                <div class="tech-stat-label">Pendientes</div>
                <div class="tech-stat-value">{{ $tecnico_stats['pendientes'] ?? 0 }}</div>
            </div>

            <div class="tech-stat-card">
                <div class="tech-stat-label">Resueltos Hoy</div>
                <div class="tech-stat-value">{{ $tecnico_stats['resueltos_hoy'] ?? 0 }}</div>
            </div>
        </div>

        <!-- ACCIONES RÁPIDAS -->
        <div class="tech-actions">
            <a href="{{ route('tickets.asignados') }}" class="tech-action-btn primary">
                <i class="bi bi-list-check"></i>
                Ver Mis Tickets
            </a>
            <button class="tech-action-btn" onclick="openTechDashboard()">
                <i class="bi bi-graph-up"></i>
                Estadísticas Detalladas
            </button>
            <button class="tech-action-btn" onclick="openReportModal()">
                <i class="bi bi-file-earmark-text"></i>
                Generar Reporte
            </button>
        </div>

        <!-- ALERTAS IMPORTANTES -->
        @if(($tecnico_stats['tickets_criticos'] ?? 0) > 0)
        <div class="tech-alerts">
            <div class="tech-alerts-title">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Alertas Activas
            </div>
            @if(($tecnico_stats['tickets_criticos'] ?? 0) > 0)
            <div class="tech-alert-item">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span><strong>{{ $tecnico_stats['tickets_criticos'] }}</strong> ticket{{ $tecnico_stats['tickets_criticos'] > 1 ? 's' : '' }} de prioridad alta</span>
            </div>
            @endif
            @if(($tecnico_stats['tickets_retrasados'] ?? 0) > 0)
            <div class="tech-alert-item">
                <i class="bi bi-clock-history"></i>
                <span><strong>{{ $tecnico_stats['tickets_retrasados'] }}</strong> ticket{{ $tecnico_stats['tickets_retrasados'] > 1 ? 's' : '' }} retrasado{{ $tecnico_stats['tickets_retrasados'] > 1 ? 's' : '' }}</span>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
    function openTechDashboard() {
        // Implementar modal o redirección a panel técnico
        alert('Panel técnico detallado - Estadísticas del equipo');
    }

    function openReportModal() {
        // Implementar modal de reporte
        alert('Generador de reportes técnicos');
    }
</script>
