{{-- VISTA PARCIAL: COMENTARIOS PREMIUM --}}

<style>
    /* Importar estilos if not in main layout */
    @import url('{{ asset("css/comments-premium.css") }}');
</style>

<div class="comments-section" data-ticket-id="{{ $ticket['id_ticket'] ?? '' }}">
    <!-- HEADER DE COMENTARIOS -->
    <div class="comments-header mb-3">
        <div>
            <h6 class="mb-0">
                <i class="bi bi-chat-dots-fill me-2" style="color: #667eea;"></i>
                Historial de Comentarios
            </h6>
        </div>
        <span class="comments-count">{{ count($comentarios ?? []) }}</span>
    </div>

    @php
        $techComentarios  = array_values(array_filter($comentarios ?? [], fn($c) => !empty($c['es_actualizacion'])));
        $otrosComentarios = array_values(array_filter($comentarios ?? [], fn($c) => empty($c['es_actualizacion'])));
        $totalComentarios = count($comentarios ?? []);
    @endphp

    {{-- ══════════ ACTUALIZACIONES TÉCNICAS (fijas arriba) ══════════ --}}
    @if(count($techComentarios) > 0)
    <div class="tech-updates-section" data-tech-section>
        <div class="tech-updates-header">
            <i class="bi bi-tools me-1"></i> ACTUALIZACIONES TÉCNICAS
        </div>
        <div class="tech-updates-body" data-tech-container>
            @foreach($techComentarios as $index => $comentario)
                @php
                    $nombreCompleto = trim(($comentario['usuario']['nombre'] ?? 'Anónimo') . ' ' . ($comentario['usuario']['apellido'] ?? ''));
                    $initials = strtoupper(substr(
                        collect(explode(' ', $nombreCompleto))->map(fn($w) => $w[0] ?? '')->join(''),
                        0, 2
                    ));
                    $rolTech     = $comentario['usuario']['rol'] ?? 'Técnico';
                    $isAdminTech = str_contains($rolTech, 'Administrador');
                    $techBadgeIcon  = $isAdminTech ? 'bi-shield-lock' : 'bi-tools';
                    $techBadgeLabel = $isAdminTech ? 'Administrador' : 'Técnico';
                @endphp
                <div class="comment-item tech-comment"
                     data-comment-id="{{ $comentario['id_comentario'] ?? $index }}">
                    <div class="comment-header">
                        <div class="comment-user-info">
                            <div class="comment-avatar">{{ $initials }}</div>
                            <div>
                                <div class="comment-username">{{ $nombreCompleto }}</div>
                                <span class="comment-role-badge">
                                    <i class="bi {{ $techBadgeIcon }} me-1"></i>{{ $techBadgeLabel }}
                                </span>
                            </div>
                        </div>
                        <div class="comment-meta">
                            <span class="comment-date">
                                {{ \Carbon\Carbon::parse($comentario['created_at'] ?? now())->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="comment-content">{{ $comentario['contenido'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="tech-updates-section" data-tech-section style="display:none">
        <div class="tech-updates-header"><i class="bi bi-tools me-1"></i> ACTUALIZACIONES TÉCNICAS</div>
        <div class="tech-updates-body" data-tech-container></div>
    </div>
    @endif

    {{-- ══════════ SEPARADOR ══════════ --}}
    <div class="separator-other-comments" data-separator
         @if(count($techComentarios) === 0 || count($otrosComentarios) === 0) style="display:none" @endif>
        OTROS COMENTARIOS
    </div>

    {{-- ══════════ OTROS COMENTARIOS ══════════ --}}
    <div class="comments-container" data-comentarios-container>
        @if(count($otrosComentarios) > 0)
            @foreach($otrosComentarios as $index => $comentario)
                @php
                    $rol = $comentario['usuario']['rol'] ?? '';
                    $cssClass = str_contains($rol, 'Administrador') ? 'admin-comment' : 'user-comment';
                    $rolLabel = str_contains($rol, 'Administrador') ? 'Administrador' : 'Usuario';
                    $rolIcon  = str_contains($rol, 'Administrador') ? 'bi-shield-lock' : 'bi-person-fill';
                    $nombreCompleto = trim(($comentario['usuario']['nombre'] ?? 'Anónimo') . ' ' . ($comentario['usuario']['apellido'] ?? ''));
                    $initials = strtoupper(substr(
                        collect(explode(' ', $nombreCompleto))->map(fn($w) => $w[0] ?? '')->join(''),
                        0, 2
                    ));
                @endphp
                <div class="comment-item {{ $cssClass }}"
                     data-comment-id="{{ $comentario['id_comentario'] ?? $index }}">
                    <div class="comment-header">
                        <div class="comment-user-info">
                            <div class="comment-avatar">{{ $initials }}</div>
                            <div>
                                <div class="comment-username">{{ $nombreCompleto }}</div>
                                <span class="comment-role-badge">
                                    <i class="bi {{ $rolIcon }} me-1"></i>{{ $rolLabel }}
                                </span>
                            </div>
                        </div>
                        <div class="comment-meta">
                            <span class="comment-date">
                                {{ \Carbon\Carbon::parse($comentario['created_at'] ?? now())->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="comment-content">{{ $comentario['contenido'] ?? '' }}</div>
                </div>
            @endforeach
        @else
            @if($totalComentarios === 0)
            <div class="no-comments">
                <div class="no-comments-icon">💬</div>
                <p class="no-comments-text">No hay comentarios aún. Los comentarios aparecerán aquí.</p>
            </div>
            @endif
        @endif
    </div>

    @if($totalComentarios > 3)
    <div class="d-flex justify-content-center mt-3">
        <button class="btn-view-all-comments" data-bs-toggle="modal" data-bs-target="#allCommentsModal" data-btn-view-all-comments>
            <i class="bi bi-arrow-down-circle me-1"></i>Ver todos ({{ $totalComentarios }})
        </button>
    </div>
    @endif

    <!-- ===== FORMULARIO DE COMENTARIOS PREMIUM ===== -->
    <div class="comment-form-section mt-4" data-comment-form-wrapper>
        <form data-comment-form data-ticket-id="{{ $ticket['id_ticket'] ?? '' }}" method="POST" action="#" onsubmit="return false;" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket['id_ticket'] ?? '' }}">
            <div class="comment-form-title">
                <i class="bi bi-pencil-square me-2"></i> Agregar Comentario
            </div>

            <div class="comment-form-group">
                <textarea 
                    class="comment-textarea"
                    name="contenido"
                    placeholder="Comparte tu comentario, avance o información importante..."
                    required></textarea>
                <small class="comment-char-count" data-char-count>0/2000</small>
            </div>

            <div class="comment-form-footer">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Los comentarios son vistos por técnicos y administradores en tiempo real
                </span>
                <button type="button" class="btn-submit-comment" data-btn-submit-comment>
                    <i class="bi bi-send me-1"></i>Enviar Comentario
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: VER TODOS LOS COMENTARIOS -->
<div class="modal fade" id="allCommentsModal" tabindex="-1" aria-labelledby="allCommentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-xl" style="border-radius: 16px; overflow: hidden;">
            <!-- Header -->
            <div class="modal-header modal-comments-header p-4 border-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="allCommentsModalLabel">
                    <i class="bi bi-chat-dots-fill"></i>
                    Todos los Comentarios
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body -->
            <div class="modal-comments-body"></div>

            <!-- Footer -->
            <div class="modal-footer bg-light border-top p-3">
                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR COMENTARIO -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="editCommentModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Editar Comentario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form data-edit-comment-form>
                @csrf
                <div class="modal-body p-4">
                    <textarea 
                        data-edit-comment-textarea
                        class="form-control form-control-lg"
                        rows="6"
                        placeholder="Actualiza tu comentario..."
                        required></textarea>
                    <small class="text-muted mt-2 d-block">Puedes editar tu comentario dentro de 5 minutos.</small>
                </div>
                
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/comments-v2.js') }}"></script>
