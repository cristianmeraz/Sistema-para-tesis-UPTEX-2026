/**
 * GESTOR DE COMENTARIOS PREMIUM
 * Maneja la visualización mejorada de comentarios en modal
 */

document.addEventListener('DOMContentLoaded', function() {
    const commentsContainer = document.querySelector('[data-comentarios-container]');
    
    if (!commentsContainer) return;

    // Obtener comentarios del contenedor
    const commentItems = commentsContainer.querySelectorAll('[data-comment-id]');
    const totalComments = commentItems.length;

    // Actualizar contador si existe
    const commentsCount = document.querySelector('.comments-count');
    if (commentsCount) {
        commentsCount.textContent = totalComments;
    }

    // Configurar colores según rol
    commentItems.forEach(item => {
        const userRole = item.dataset.commentUserRole || 'usuario';
        item.classList.add(`comment-${userRole}`);

        // Avatar inicial del usuario
        const avatar = item.querySelector('.comment-avatar');
        if (avatar) {
            const initials = item.dataset.commentUserName
                ?.split(' ')
                .map(word => word.charAt(0))
                .join('')
                .toUpperCase()
                .substring(0, 2) || '?';
            avatar.textContent = initials;
        }

        // Badge de rol
        const roleBadge = item.querySelector('.comment-role-badge');
        if (roleBadge) {
            roleBadge.classList.add(`role-${userRole}`);
        }

        // Formatear fecha
        const dateElement = item.querySelector('.comment-date');
        if (dateElement && dateElement.dataset.timestamp) {
            dateElement.textContent = formatRelativeTime(new Date(dateElement.dataset.timestamp));
        }
    });

    // Botón "Ver Todos los Comentarios"
    const viewAllBtn = document.querySelector('[data-btn-view-all-comments]');
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', showAllCommentsModal);
    }

    // Función: Formatear hora relativa
    function formatRelativeTime(date) {
        const now = new Date();
        const diffMs = now - date;
        const diffSecs = Math.floor(diffMs / 1000);
        const diffMins = Math.floor(diffSecs / 60);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffSecs < 60) return 'Hace unos segundos';
        if (diffMins < 60) return `Hace ${diffMins} minuto${diffMins > 1 ? 's' : ''}`;
        if (diffHours < 24) return `Hace ${diffHours} hora${diffHours > 1 ? 's' : ''}`;
        if (diffDays < 7) return `Hace ${diffDays} día${diffDays > 1 ? 's' : ''}`;
        
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Función: Mostrar modal con todos los comentarios
    function showAllCommentsModal() {
        const modal = new (window.bootstrap?.Modal || function() {})('#allCommentsModal', {
            backdrop: 'static',
            keyboard: false
        });

        // Generar HTML con todos los comentarios
        const allCommentsHtml = Array.from(commentItems).map(item => {
            const userRole = item.dataset.commentUserRole || 'usuario';
            const userName = item.dataset.commentUserName || 'Anónimo';
            const timestamp = item.querySelector('.comment-date')?.dataset.timestamp || new Date().toISOString();
            const content = item.querySelector('.comment-content')?.textContent || '';
            const stateBadge = item.querySelector('.comment-state-badge');

            const initials = userName.split(' ')
                .map(word => word.charAt(0))
                .join('')
                .toUpperCase()
                .substring(0, 2);

            return `
                <div class="modal-comment-item item-${userRole}">
                    <div class="comment-header" style="margin-bottom: 1rem;">
                        <div class="comment-user-info">
                            <div class="comment-avatar" style="width: 40px; height: 40px;">${initials}</div>
                            <div>
                                <div class="comment-username">${escapeHtml(userName)}</div>
                                <span class="comment-role-badge role-${userRole}">${getUserRoleLabel(userRole)}</span>
                            </div>
                        </div>
                        <div class="comment-meta" style="text-align: right;">
                            <span class="comment-date">${formatRelativeTime(new Date(timestamp))}</span>
                            ${stateBadge ? '<span class="' + stateBadge.className + '">' + stateBadge.textContent + '</span>' : ''}
                        </div>
                    </div>
                    <div class="comment-content">${escapeHtml(content)}</div>
                </div>
            `;
        }).join('');

        const modalBody = document.querySelector('.modal-comments-body');
        if (modalBody) {
            modalBody.innerHTML = allCommentsHtml || '<div class="no-comments"><div class="no-comments-icon">💬</div><p class="no-comments-text">No hay comentarios</p></div>';
        }

        if (modal.show) modal.show();
    }

    // Función auxiliar: Escaper HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Función auxiliar: Obtener etiqueta de rol
    function getUserRoleLabel(role) {
        const labels = {
            'tecnico': 'Técnico',
            'admin': 'Administrador',
            'usuario': 'Usuario'
        };
        return labels[role] || 'Usuario';
    }

    // Auto-refresh de comentarios (cada 15 segundos para admin)
    const userRole = document.body.dataset.userRole || '';
    if (userRole.includes('Administrador')) {
        setInterval(refreshComments, 15000);
    }

    function refreshComments() {
        const ticketId = document.querySelector('[data-ticket-id]')?.dataset.ticketId;
        if (!ticketId) return;

        fetch(`/api/tickets/${ticketId}/comentarios`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.comentarios && data.comentarios.length > currentCommentCount) {
                // Mostrar notificación de nuevos comentarios
                showNewCommentsNotification(data.comentarios.length - currentCommentCount);
                currentCommentCount = data.comentarios.length;
            }
        })
        .catch(error => console.error('Error al refrescar comentarios:', error));
    }

    let currentCommentCount = commentItems.length;

    function showNewCommentsNotification(newCount) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show';
        notification.innerHTML = `
            <i class="bi bi-info-circle me-2"></i>
            <strong>${newCount} nuevo${newCount > 1 ? 's' : ''} comentario${newCount > 1 ? 's' : ''}!</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.page-container') || document.body;
        container.insertBefore(notification, container.firstChild);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});
