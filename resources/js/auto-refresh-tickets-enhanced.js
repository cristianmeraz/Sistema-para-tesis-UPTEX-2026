/**
 * AUTO-REFRESH MEJORADO - TICKETS
 * Sistema inteligente de actualización automática
 * Solo para Administrador
 */

class TicketAutoRefresh {
    constructor() {
        this.REFRESH_INTERVALS = {
            fast: 15000,    // 15 segundos
            normal: 30000,  // 30 segundos
            slow: 60000     // 60 segundos
        };
        
        this.currentInterval = this.REFRESH_INTERVALS.normal;
        this.isRefreshing = false;
        this.lastUpdateTime = new Date();
        this.updateCount = 0;
        this.isEnabled = true;
        
        this.init();
    }

    init() {
        const userRole = document.body.dataset.userRole || '';
        
        // Solo para Administrador
        if (!userRole.includes('Administrador') && !userRole.includes('admin')) {
            return;
        }

        this.setupRefreshControl();
        this.startAutoRefresh();
        this.setupEventListeners();
    }

    setupRefreshControl() {
        // Crear panel de control de refresh
        const controlPanel = document.createElement('div');
        controlPanel.id = 'refreshControlPanel';
        controlPanel.className = 'refresh-control-panel';
        controlPanel.innerHTML = `
            <div class="refresh-control-container">
                <div class="refresh-status">
                    <span class="refresh-indicator" id="refreshIndicator"></span>
                    <span class="refresh-text">Autoactualización <strong>ACTIVA</strong></span>
                </div>
                
                <div class="refresh-controls">
                    <button class="refresh-btn refresh-btn-toggle" id="toggleRefreshBtn" title="Activar/Desactivar autoactualización">
                        <i class="bi bi-pause-circle"></i> Pausar
                    </button>
                    <button class="refresh-btn refresh-btn-manual" id="manualRefreshBtn" title="Actualizar ahora">
                        <i class="bi bi-arrow-clockwise"></i> Ahora
                    </button>
                    
                    <div class="refresh-speed-control">
                        <select id="refreshSpeedSelect" title="Velocidad de actualización">
                            <option value="fast">Rápido (15s)</option>
                            <option value="normal" selected>Normal (30s)</option>
                            <option value="slow">Lento (60s)</option>
                        </select>
                    </div>
                </div>
            </div>
        `;

        const pageContainer = document.querySelector('.page-container');
        if (pageContainer) {
            pageContainer.insertBefore(controlPanel, pageContainer.firstChild);
        }

        this.setupControlHandlers();
    }

    setupControlHandlers() {
        // Toggle refresh
        const toggleBtn = document.getElementById('toggleRefreshBtn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleRefresh());
        }

        // Manual refresh
        const manualBtn = document.getElementById('manualRefreshBtn');
        if (manualBtn) {
            manualBtn.addEventListener('click', () => this.forceRefresh());
        }

        // Cambiar velocidad
        const speedSelect = document.getElementById('refreshSpeedSelect');
        if (speedSelect) {
            speedSelect.addEventListener('change', (e) => this.changeRefreshSpeed(e.target.value));
        }
    }

    setupEventListeners() {
        // Pausar refresh cuando el usuario interactúa
        document.addEventListener('click', () => {
            if (this.isRefreshing) return;
            // Resetear inactividad
            this.lastUserInteraction = new Date();
        });

        // Pausar si el tab no está activo
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseRefresh();
            } else {
                this.resumeRefresh();
            }
        });
    }

    startAutoRefresh() {
        if (this.refreshInterval) clearInterval(this.refreshInterval);
        
        this.refreshInterval = setInterval(() => {
            if (this.isEnabled && !this.isRefreshing) {
                this.refresh();
            }
        }, this.currentInterval);

        console.log(`🔄 Auto-refresh iniciado: ${this.currentInterval}ms`);
    }

    toggleRefresh() {
        this.isEnabled = !this.isEnabled;
        const btn = document.getElementById('toggleRefreshBtn');
        const text = document.querySelector('.refresh-text');
        
        if (this.isEnabled) {
            btn.innerHTML = '<i class="bi bi-pause-circle"></i> Pausar';
            btn.classList.remove('paused');
            text.innerHTML = 'Autoactualización <strong>ACTIVA</strong>';
            this.startAutoRefresh();
        } else {
            btn.innerHTML = '<i class="bi bi-play-circle"></i> Reanudar';
            btn.classList.add('paused');
            text.innerHTML = 'Autoactualización <strong>PAUSADA</strong>';
            if (this.refreshInterval) clearInterval(this.refreshInterval);
        }
    }

    pauseRefresh() {
        if (this.refreshInterval) clearInterval(this.refreshInterval);
    }

    resumeRefresh() {
        if (this.isEnabled) {
            this.startAutoRefresh();
        }
    }

    forceRefresh() {
        this.refresh();
    }

    changeRefreshSpeed(speed) {
        this.currentInterval = this.REFRESH_INTERVALS[speed] || this.REFRESH_INTERVALS.normal;
        this.startAutoRefresh();
    }

    refresh() {
        if (this.isRefreshing) return;

        const table = document.querySelector('.table-wrapper table') || document.querySelector('table');
        if (!table) return;

        this.isRefreshing = true;
        this.updateRefreshIndicator('loading');

        // Obtener filtros actuales
        const filters = this.getCurrentFilters();
        const params = new URLSearchParams(filters);
        const url = window.location.pathname + '?' + params.toString();

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
                'Cache-Control': 'no-cache'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newTable = newDoc.querySelector('.table-wrapper table') || newDoc.querySelector('table');

            if (newTable) {
                this.updateTableContent(table, newTable);
                this.showRefreshNotification();
                this.updateCount++;
            }
        })
        .catch(error => {
            console.error('❌ Error durante auto-refresh:', error);
            this.updateRefreshIndicator('error');
        })
        .finally(() => {
            this.isRefreshing = false;
            this.lastUpdateTime = new Date();
            this.updateRefreshIndicator('success');
        });
    }

    getCurrentFilters() {
        const filters = {};
        
        const estadoSelect = document.querySelector('select[name="estado_id"]');
        const prioridadSelect = document.querySelector('select[name="prioridad_id"]');
        const areaSelect = document.querySelector('select[name="area_id"]');

        if (estadoSelect) filters.estado_id = estadoSelect.value;
        if (prioridadSelect) filters.prioridad_id = prioridadSelect.value;
        if (areaSelect) filters.area_id = areaSelect.value;

        return filters;
    }

    updateTableContent(oldTable, newTable) {
        const oldTbody = oldTable.querySelector('tbody');
        const newTbody = newTable.querySelector('tbody');

        if (oldTbody && newTbody) {
            // Fade out
            oldTbody.style.opacity = '0.5';
            oldTbody.style.transform = 'scale(0.98)';

            setTimeout(() => {
                oldTbody.innerHTML = newTbody.innerHTML;
                oldTbody.style.opacity = '1';
                oldTbody.style.transform = 'scale(1)';
                oldTbody.style.transition = 'all 0.3s ease-in';
            }, 150);
        }
    }

    updateRefreshIndicator(status) {
        const indicator = document.getElementById('refreshIndicator');
        if (!indicator) return;

        indicator.className = 'refresh-indicator';
        
        if (status === 'loading') {
            indicator.classList.add('loading');
            indicator.title = 'Actualizando...';
        } else if (status === 'success') {
            indicator.classList.add('success');
            indicator.title = 'Última actualización: ' + this.lastUpdateTime.toLocaleTimeString();
            
            setTimeout(() => {
                indicator.classList.remove('success');
            }, 2000);
        } else if (status === 'error') {
            indicator.classList.add('error');
            indicator.title = 'Error en la actualización';
            
            setTimeout(() => {
                indicator.classList.remove('error');
            }, 3000);
        }
    }

    showRefreshNotification() {
        const notification = document.createElement('div');
        notification.className = 'refresh-notification';
        notification.innerHTML = `
            <i class="bi bi-check-circle-fill me-2"></i>
            <span>Actualizado - Cambios detectados</span>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'fadeOut 0.3s ease-out forwards';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }
}

// Estilos para el sistema de refresh
const refreshStyles = `
    .refresh-control-panel {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }

    .refresh-control-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .refresh-status {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
    }

    .refresh-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
    }

    .refresh-indicator.loading {
        animation: spin 1s linear infinite;
        background-color: #fbbf24;
    }

    .refresh-indicator.success {
        animation: pulse 0.6s ease-out;
        background-color: #10b981;
    }

    .refresh-indicator.error {
        animation: shake 0.5s ease-out;
        background-color: #ef4444;
    }

    .refresh-controls {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .refresh-btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .refresh-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    .refresh-btn.paused {
        background: rgba(255, 255, 255, 0.1);
    }

    .refresh-speed-control select {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .refresh-speed-control select:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .refresh-speed-control select option {
        background: #2d3748;
        color: white;
    }

    .refresh-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        animation: slideInUp 0.3s ease-out;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.5); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateY(10px);
        }
    }

    @media (max-width: 768px) {
        .refresh-control-container {
            flex-direction: column;
            align-items: flex-start;
        }

        .refresh-controls {
            width: 100%;
        }

        .refresh-notification {
            bottom: 10px;
            right: 10px;
            left: 10px;
        }
    }
`;

// Inyectar estilos
const styleSheet = document.createElement('style');
styleSheet.textContent = refreshStyles;
document.head.appendChild(styleSheet);

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new TicketAutoRefresh();
});
