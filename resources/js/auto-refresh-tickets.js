/**
 * AUTO-REFRESH TICKETS - FASE 2
 * Actualiza automáticamente la lista de tickets cada 30 segundos
 * Solo para usuario Administrador
 */

document.addEventListener('DOMContentLoaded', function() {
    const REFRESH_INTERVAL = 30000; // 30 segundos
    const userRole = document.body.dataset.userRole || 'Usuario';
    
    // Solo activar para Administrador
    if (!userRole.includes('Administrador')) {
        return;
    }
    
    let isRefreshing = false;
    
    const autoRefreshTickets = function() {
        if (isRefreshing) return;
        
        // Si no estamos en la página de tickets, no refrescar
        const ticketsTable = document.getElementById('ticketsTable');
        if (!ticketsTable) return;
        
        isRefreshing = true;
        
        // Obtener el estado actual de los filtros
        const filters = {
            estado_id: document.getElementById('estadoFilter')?.value || '',
            prioridad_id: document.getElementById('prioridadFilter')?.value || '',
            area_id: document.getElementById('areaFilter')?.value || '',
        };
        
        // Crear URL con parámetros
        const params = new URLSearchParams(filters);
        const url = window.location.pathname + '?' + params.toString();
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
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
            const newTable = newDoc.getElementById('ticketsTable');
            
            if (newTable) {
                // Mostrar badge de "actualizado"
                showRefreshBadge();
                
                // Actualizar tbody con fade-in effect
                const tbody = ticketsTable.querySelector('tbody');
                const newTbody = newTable.querySelector('tbody');
                
                if (tbody && newTbody) {
                    // Fade out
                    tbody.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        tbody.innerHTML = newTbody.innerHTML;
                        tbody.style.opacity = '1';
                        tbody.style.transition = 'opacity 0.3s ease-in';
                    }, 150);
                }
            }
        })
        .catch(error => {
            console.error('Error durante auto-refresh:', error);
        })
        .finally(() => {
            isRefreshing = false;
        });
    };
    
    // Mostrar badge temporal indicando que se actualizó
    const showRefreshBadge = function() {
        let badge = document.querySelector('.refresh-badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'refresh-badge';
            const header = document.querySelector('h2');
            if (header) header.appendChild(badge);
        }
        badge.textContent = '✓ Actualizado ahora';
        badge.style.animation = 'fadeInOut 3s ease-in-out';
    };
    
    // Agregar CSS para la animación
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }
        .refresh-badge {
            display: inline-block !important;
            padding: 0.4rem 0.8rem;
            background-color: #D4EDDA;
            color: #155724;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 1rem;
        }
    `;
    document.head.appendChild(style);
    
    // Iniciar auto-refresh
    console.log('🔄 Auto-refresh de tickets activado cada 30 segundos');
    setInterval(autoRefreshTickets, REFRESH_INTERVAL);
});
