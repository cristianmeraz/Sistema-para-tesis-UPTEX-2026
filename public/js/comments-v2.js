/**
 * SISTEMA DE COMENTARIOS - UPTEX Tickets
 * Gestion completa con auto-refresh y validaciones
 */

const CommentSystem = (() => {
    const config = {
        pollInterval: 15000, // 15 segundos (evita saturar php artisan serve single-thread)
        maxCharacters: 2000,
        minCharacters: 5,
        apiBaseUrl: '/w'
    };

    let pollTimer = null;
    let lastCommentCount = 0;
    let isBusy = false; // Mutex para evitar peticiones simultaneas

    // ============= INICIALIZACION =============
    const init = () => {
        let ticketId = document.querySelector('[data-ticket-id]')?.getAttribute('data-ticket-id');
        if (!ticketId) {
            ticketId = document.querySelector('[data-comment-form]')?.getAttribute('data-ticket-id');
        }
        if (!ticketId) {
            const match = window.location.pathname.match(/\/tickets\/(\d+)/);
            ticketId = match ? match[1] : null;
        }
        
        if (!ticketId) return;

        console.log('[Comentarios] Ticket ID:', ticketId);
        setupFormHandlers(ticketId);
        setupCharCounter();
        setupEditHandlers(ticketId);
        startAutoPolling(ticketId);
    };

    // ============= MANEJADORES DE FORMULARIO =============
    const setupFormHandlers = (ticketId) => {
        const form = document.querySelector('[data-comment-form]');
        const submitBtn = document.querySelector('[data-btn-submit-comment]');
        if (!form || !submitBtn) return;

        submitBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            stopAutoPolling(); // Pausar polling
            await submitComment(ticketId, form);
            startAutoPolling(ticketId); // Reanudar polling
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    };

    // ============= ENVIAR COMENTARIO =============
    const submitComment = async (ticketId, form) => {
        const textarea = form.querySelector('textarea[name="contenido"]');
        const submitBtn = document.querySelector('[data-btn-submit-comment]');
        if (!textarea || !submitBtn) return;

        const content = textarea.value.trim();
        if (!validateComment(content)) return;

        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Guardando...';

        try {
            const response = await fetch(config.apiBaseUrl + '/tickets/' + ticketId + '/comentarios', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]')?.value || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ contenido: content })
            });

            const data = await response.json();

            if (!response.ok) {
                showError(data.message || data.error || 'Error al guardar comentario');
                return;
            }

            showSuccess('Comentario guardado exitosamente');
            form.reset();
            updateCharCounter();
            
            // Esperar un momento antes de refrescar
            await new Promise(r => setTimeout(r, 300));
            await refreshComments(ticketId);

        } catch (error) {
            console.error('[Comentarios] Error POST:', error);
            showError('Error al enviar. Intenta de nuevo.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    };

    // ============= VALIDACION =============
    const validateComment = (content) => {
        if (content.length < config.minCharacters) {
            showError('Minimo ' + config.minCharacters + ' caracteres');
            return false;
        }
        if (content.length > config.maxCharacters) {
            showError('Maximo ' + config.maxCharacters + ' caracteres');
            return false;
        }
        return true;
    };

    // ============= CONTADOR DE CARACTERES =============
    const setupCharCounter = () => {
        const textarea = document.querySelector('[data-comment-form] textarea[name="contenido"]');
        if (!textarea) return;
        textarea.addEventListener('input', updateCharCounter);
        updateCharCounter();
    };

    const updateCharCounter = () => {
        const textarea = document.querySelector('[data-comment-form] textarea[name="contenido"]');
        const counter = document.querySelector('[data-char-count]');
        if (!textarea || !counter) return;
        const length = textarea.value.length;
        counter.textContent = length + '/' + config.maxCharacters;
        counter.classList.toggle('warning', length > config.maxCharacters * 0.9);
    };

    // ============= EDITAR Y ELIMINAR COMENTARIOS =============
    const setupEditHandlers = (ticketId) => {
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-btn-edit-comment]')) {
                const btn = e.target.closest('[data-btn-edit-comment]');
                const commentId = btn.dataset.commentId;
                const content = btn.closest('.comment-item')?.querySelector('.comment-content')?.textContent;
                openEditModal(ticketId, commentId, content);
            }
            if (e.target.closest('[data-btn-delete-comment]')) {
                const commentId = e.target.closest('[data-btn-delete-comment]').dataset.commentId;
                if (confirm('Eliminar este comentario?')) {
                    deleteComment(ticketId, commentId);
                }
            }
        });
    };

    const openEditModal = (ticketId, commentId, content) => {
        const modalEl = document.getElementById('editCommentModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        const textarea = document.querySelector('[data-edit-comment-textarea]');
        if (textarea) textarea.value = content;
        modal.show();

        const form = document.querySelector('[data-edit-comment-form]');
        if (form) {
            form.onsubmit = async (e) => {
                e.preventDefault();
                await submitEditComment(ticketId, commentId, form, modal);
            };
        }
    };

    const submitEditComment = async (ticketId, commentId, form, modal) => {
        const textarea = form.querySelector('[data-edit-comment-textarea]');
        const submitBtn = form.querySelector('button[type="submit"]');
        const content = textarea?.value?.trim();
        if (!content || !validateComment(content)) return;

        if (submitBtn) submitBtn.disabled = true;

        try {
            const response = await fetch(config.apiBaseUrl + '/tickets/' + ticketId + '/comentarios/' + commentId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]')?.value || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ contenido: content })
            });
            const data = await response.json();
            if (!response.ok) { showError(data.error || 'Error al actualizar'); return; }
            showSuccess('Comentario actualizado');
            modal.hide();
            await refreshComments(ticketId);
        } catch (error) {
            showError('Error al actualizar comentario');
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    };

    const deleteComment = async (ticketId, commentId) => {
        try {
            const response = await fetch(config.apiBaseUrl + '/tickets/' + ticketId + '/comentarios/' + commentId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]')?.value || '',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (!response.ok) { showError(data.error || 'Error al eliminar'); return; }
            showSuccess('Comentario eliminado');
            await refreshComments(ticketId);
        } catch (error) {
            showError('Error al eliminar comentario');
        }
    };

    // ============= AUTO-REFRESH DE COMENTARIOS =============
    const startAutoPolling = (ticketId) => {
        stopAutoPolling();
        refreshComments(ticketId);
        pollTimer = setInterval(() => refreshComments(ticketId), config.pollInterval);
    };

    const stopAutoPolling = () => {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    };

    const refreshComments = async (ticketId) => {
        if (isBusy) return;
        isBusy = true;

        try {
            const response = await fetch(config.apiBaseUrl + '/tickets/' + ticketId + '/comentarios', {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) { isBusy = false; return; }

            const data = await response.json();
            const comentarios = data.comentarios || [];
            
            const comentariosArray = Array.isArray(comentarios) ? comentarios : Object.values(comentarios);
            
            updateCommentsDOM(comentariosArray);

            if (comentariosArray.length > lastCommentCount && lastCommentCount > 0) {
                playNotificationSound();
            }
            lastCommentCount = comentariosArray.length;

        } catch (error) {
            console.warn('[Comentarios] Polling error (se reintentara):', error.message);
        } finally {
            isBusy = false;
        }
    };

    // ============= ACTUALIZAR DOM =============
    const updateCommentsDOM = (comentarios) => {
        const techContainer  = document.querySelector('[data-tech-container]');
        const techSection    = document.querySelector('[data-tech-section]');
        const otherContainer = document.querySelector('[data-comentarios-container]');
        const separator      = document.querySelector('[data-separator]');

        const techComments  = comentarios.filter(c => c.es_actualizacion === true);
        const otherComments = comentarios.filter(c => c.es_actualizacion !== true);

        // Sección técnica
        if (techSection)  techSection.style.display  = techComments.length > 0 ? '' : 'none';
        if (techContainer) techContainer.innerHTML = techComments.map(c => createCommentHTML(c, 'tech')).join('');

        // Separador: solo visible si hay ambos grupos
        if (separator) separator.style.display = (techComments.length > 0 && otherComments.length > 0) ? '' : 'none';

        // Otros comentarios
        if (otherContainer) {
            if (otherComments.length > 0) {
                otherContainer.innerHTML = otherComments.map(c => createCommentHTML(c)).join('');
            } else if (comentarios.length === 0) {
                otherContainer.innerHTML = '<div class="no-comments"><div class="no-comments-icon">&#x1F4AC;</div><p class="no-comments-text">No hay comentarios aún.</p></div>';
            } else {
                otherContainer.innerHTML = '';
            }
        }

        const countBadge = document.querySelector('.comments-count');
        if (countBadge) countBadge.textContent = comentarios.length;
    };

    // HTML para cualquier comentario — 'forceClass' fuerza tech-comment desde el polling
    const createCommentHTML = (comment, forceClass) => {
        const user    = comment.usuario || {};
        const rol     = user.rol?.nombre || 'Usuario';
        const initials = getInitials(user.nombre, user.apellido);

        let cssClass, rolLabel, rolIcon;
        if (forceClass === 'tech' || comment.es_actualizacion === true) {
            cssClass = 'tech-comment';
            rolLabel = 'Técnico';
            rolIcon  = '<i class="bi bi-tools me-1"></i>';
        } else if (rol.includes('Admin')) {
            cssClass = 'admin-comment';
            rolLabel = 'Administrador';
            rolIcon  = '<i class="bi bi-shield-lock me-1"></i>';
        } else {
            cssClass = 'user-comment';
            rolLabel = 'Usuario';
            rolIcon  = '<i class="bi bi-person-fill me-1"></i>';
        }

        return '<div class="comment-item ' + cssClass + '" data-comment-id="' + comment.id_comentario + '">' +
            '<div class="comment-header">' +
                '<div class="comment-user-info">' +
                    '<div class="comment-avatar">' + initials + '</div>' +
                    '<div>' +
                        '<div class="comment-username">' + escapeHtml((user.nombre || 'Anónimo') + ' ' + (user.apellido || '')) + '</div>' +
                        '<span class="comment-role-badge">' + rolIcon + escapeHtml(rolLabel) + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="comment-meta">' +
                    '<span class="comment-date" title="' + (comment.created_at || '') + '">' + formatDate(comment.created_at) + '</span>' +
                '</div>' +
            '</div>' +
            '<div class="comment-content">' + escapeHtml(comment.contenido) + '</div>' +
            '<div class="comment-actions" style="display:flex;gap:0.5rem;margin-top:0.5rem;">' +
                '<button type="button" class="btn btn-sm btn-outline-primary" data-btn-edit-comment data-comment-id="' + comment.id_comentario + '">' +
                    '<i class="bi bi-pencil"></i> Editar' +
                '</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger" data-btn-delete-comment data-comment-id="' + comment.id_comentario + '">' +
                    '<i class="bi bi-trash"></i> Eliminar' +
                '</button>' +
            '</div>' +
        '</div>';
    };

    // ============= UTILIDADES =============
    const getInitials = (nombre, apellido) => {
        const f = (nombre || '').charAt(0).toUpperCase();
        const s = (apellido || '').charAt(0).toUpperCase();
        return (f + s) || 'U';
    };

    const getRoleIcon = (role) => {
        const icons = { 'admin': '<i class="bi bi-shield-lock"></i>', 'tecnico': '<i class="bi bi-tools"></i>', 'usuario': '<i class="bi bi-person-fill"></i>' };
        return icons[role] || icons['usuario'];
    };

    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    };

    const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        if (minutes < 1) return 'Ahora';
        if (minutes < 60) return 'Hace ' + minutes + 'm';
        if (hours < 24) return 'Hace ' + hours + 'h';
        if (days < 7) return 'Hace ' + days + 'd';
        return date.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: '2-digit' });
    };

    // ============= NOTIFICACIONES =============
    const showSuccess = (msg) => showNotification(msg, 'success');
    const showError = (msg) => showNotification(msg, 'error');

    const showNotification = (message, type) => {
        type = type || 'info';
        const cls = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const icon = type === 'success' ? '&#x2713;' : type === 'error' ? '&#x2717;' : 'i';
        const alert = document.createElement('div');
        alert.className = 'alert ' + cls + ' alert-dismissible fade show';
        alert.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;';
        alert.innerHTML = '<span>' + icon + ' ' + message + '</span><button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 4000);
    };

    const playNotificationSound = () => {
        const audio = new Audio('data:audio/wav;base64,UklGRiYAAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQIAAAAAAA==');
        audio.play().catch(() => {});
    };

    window.addEventListener('beforeunload', () => stopAutoPolling());

    return { init, refreshComments, destroy: stopAutoPolling };
})();

// Iniciar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', CommentSystem.init);
} else {
    CommentSystem.init();
}
