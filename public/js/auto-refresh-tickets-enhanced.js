/**
 * AUTO-REFRESH DASHBOARD ADMIN - UPTEX
 * Actualiza stats, alertas y tarjetas cada 30 segundos via AJAX
 * Incluye feedback visual en cada actualizacion
 */
class AdminDashboardRefresh {
    constructor() {
        this.interval = 30000;
        this.timer = null;
        this.isBusy = false;
        this.refreshUrl = '/reportes/refresh-stats';
        this.init();
    }

    init() {
        // Obtener URL del script tag si existe
        var scriptTag = document.querySelector('script[data-refresh-url]');
        if (scriptTag && scriptTag.dataset.refreshUrl) {
            this.refreshUrl = scriptTag.dataset.refreshUrl;
        }

        console.log('[Dashboard] Auto-refresh activo (' + (this.interval / 1000) + 's). URL:', this.refreshUrl);
        
        // Primer refresh a los 3 segundos (dar tiempo a que la pagina cargue)
        setTimeout(function() { this.refresh(); }.bind(this), 3000);
        
        // Luego cada 30 segundos
        this.timer = setInterval(function() { this.refresh(); }.bind(this), this.interval);
    }

    stop() {
        if (this.timer) { clearInterval(this.timer); this.timer = null; }
    }

    async refresh() {
        if (this.isBusy) return;
        this.isBusy = true;

        // Indicador visual: punto verde parpadea rapido durante refresh
        var indicator = document.getElementById('liveIndicator');
        if (indicator) indicator.style.animation = 'livePulse 0.5s ease-in-out infinite';

        try {
            var response = await fetch(this.refreshUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) {
                console.warn('[Dashboard] Refresh HTTP ' + response.status);
                return;
            }

            var data = await response.json();
            if (!data.success || !data.stats) {
                console.warn('[Dashboard] Respuesta sin stats');
                return;
            }

            this.updateAllStats(data.stats);
            
            // Actualizar timestamp
            var tsEl = document.getElementById('lastUpdate');
            if (tsEl) tsEl.textContent = data.timestamp || new Date().toLocaleTimeString('es-MX', {hour:'2-digit', minute:'2-digit'});

            console.log('[Dashboard] Actualizado:', data.timestamp);

        } catch (e) {
            console.warn('[Dashboard] Error refresh:', e.message);
        } finally {
            this.isBusy = false;
            // Restaurar animacion normal del indicador
            if (indicator) indicator.style.animation = 'livePulse 2s ease-in-out infinite';
        }
    }

    updateAllStats(stats) {
        var hasChanges = false;

        // Recorrer todas las keys del objeto stats
        for (var key in stats) {
            if (!stats.hasOwnProperty(key)) continue;
            var newVal = String(stats[key]);
            var elements = document.querySelectorAll('[data-stat="' + key + '"]');
            
            for (var i = 0; i < elements.length; i++) {
                var el = elements[i];
                var oldVal = el.textContent.trim();
                
                if (oldVal !== newVal) {
                    hasChanges = true;
                    el.textContent = newVal;
                    
                    // Animacion de escala + flash en el contenedor padre
                    el.style.transition = 'transform 0.3s ease';
                    el.style.transform = 'scale(1.2)';
                    
                    var card = el.closest('.stat-card, .priority-card');
                    if (card) {
                        card.classList.add('stat-updated');
                        (function(c) {
                            setTimeout(function() { c.classList.remove('stat-updated'); }, 1200);
                        })(card);
                    }
                    
                    (function(e) {
                        setTimeout(function() { e.style.transform = 'scale(1)'; }, 400);
                    })(el);
                }
            }
        }

        // Resueltos hoy en panel tecnico
        var techRes = document.querySelector('[data-stat="resueltos_hoy_tech"]');
        if (techRes && stats.resueltos_hoy !== undefined) {
            techRes.textContent = String(stats.resueltos_hoy);
        }

        // Mostrar/ocultar alerta critica
        var alertaCritica = document.getElementById('alerta-critica');
        if (alertaCritica) {
            var criticos = parseInt(stats.prioridad_critica) || 0;
            alertaCritica.style.display = criticos > 0 ? '' : 'none';
        }

        // Flash visual en el badge de timestamp para confirmar que hubo refresh
        var badge = document.getElementById('badgeUpdate');
        if (badge) {
            badge.style.transition = 'background 0.3s ease';
            badge.style.background = hasChanges ? '#059669' : '#0dcaf0';
            setTimeout(function() { badge.style.background = ''; }, 1500);
        }
    }
}

// Iniciar - compatible con carga tardia (despues de DOMContentLoaded)
(function() {
    function startRefresh() {
        var container = document.querySelector('[data-user-role="Administrador"]');
        if (container) {
            window._adminRefresh = new AdminDashboardRefresh();
        } else {
            console.log('[Dashboard] No es dashboard admin, auto-refresh no activado');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startRefresh);
    } else {
        // DOM ya esta listo (script cargado tarde via @stack)
        startRefresh();
    }
})();
