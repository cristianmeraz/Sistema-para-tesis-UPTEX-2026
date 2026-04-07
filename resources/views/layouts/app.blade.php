<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Tickets UPTEX')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --secondary: #64748B;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
            --dark: #1E293B;
            --light: #F8FAFC;
            --sidebar-width: 260px;
            --header-height: 64px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
            overflow-x: hidden;
        }
        
        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
            color: white;
            z-index: 1050;
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
        }
        
        /* HEADER */
        .main-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1000;
            transition: left 0.3s ease;
        }
        
        /* CONTENIDO PRINCIPAL */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 1.5rem;
            min-height: calc(100vh - var(--header-height));
            transition: margin-left 0.3s ease;
        }

        /* CAPA OSCURA MÓVIL */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        .sidebar-header { padding: 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: white; }
        .sidebar-logo-img { height: 38px; width: auto; background: white; border-radius: 7px; padding: 3px 7px; display: block; flex-shrink: 0; }
        .sidebar-logo-text h4 { font-size: 1.1rem; font-weight: 700; margin: 0; }
        .sidebar-logo-text p { font-size: 0.75rem; color: rgba(255,255,255,0.6); margin: 0; }

        .sidebar-nav { padding: 1rem 0; }
        .nav-section-title { padding: 1rem 1.5rem 0.5rem; font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.5px; }
        .nav-item { margin: 0.25rem 0.75rem; }
        .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 8px; transition: all 0.2s ease; font-size: 0.9rem; }
        .nav-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background: var(--primary); color: white; }

        /* RESPONSIVIDAD */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0) !important; }
            .main-header, .main-content { margin-left: 0; left: 0; }
            .sidebar-overlay.show { display: block !important; }
        }

        /* BREADCRUMBS */
        .header-breadcrumbs {
            display: flex; align-items: center; gap: .35rem; flex-wrap: wrap;
        }
        .bc-link {
            font-size: .84rem; color: #64748b; text-decoration: none;
            display: inline-flex; align-items: center; gap: .3rem;
            transition: color .15s;
        }
        .bc-link:hover { color: #4f46e5; }
        .bc-sep { font-size: .6rem; color: #cbd5e1; }
        .bc-current {
            font-size: .84rem; font-weight: 700; color: #1e293b;
            display: inline-flex; align-items: center; gap: .3rem;
        }

        /* HEADER AVATAR */
        .hdr-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            color: white; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem; flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,.15);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-logo">
                <img src="{{ asset('images/logo-uptex.png') }}" alt="UPTEX" class="sidebar-logo-img">
                <div class="sidebar-logo-text">
                    <h4>UPTEX Tickets</h4>
                    <p>Sistema de Soporte</p>
                </div>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section-title">Principal</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>
                        @if(session('usuario_rol') == 'Administrador') Panel de Administrador 
                        @elseif(session('usuario_rol') == 'Técnico') Panel de Trabajo 
                        @else Mi Dashboard @endif
                    </span>
                </a>
            </div>
            
            @if(session('usuario_rol') == 'Administrador')
                <div class="nav-section-title">Gestión</div>
                <div class="nav-item">
                    <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Usuarios</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Estadísticas</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                        <i class="bi bi-ticket"></i>
                        <span>Ver Tickets</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('papelera.index') }}" class="nav-link {{ request()->routeIs('papelera.*') ? 'active' : '' }}">
                        <i class="bi bi-trash3"></i>
                        <span>Papelera</span>
                    </a>
                </div>
            @endif

            @if(session('usuario_rol') == 'Técnico')
                <div class="nav-section-title">Trabajo Técnico</div>
                <div class="nav-item">
                    <a href="{{ route('tickets.mis-tickets') }}" class="nav-link {{ request()->routeIs('tickets.mis-tickets') ? 'active' : '' }}">
                        <i class="bi bi-list-task"></i>
                        <span>Todos los Tickets</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('papelera.index') }}" class="nav-link {{ request()->routeIs('papelera.*') ? 'active' : '' }}">
                        <i class="bi bi-trash3"></i>
                        <span>Papelera</span>
                    </a>
                </div>
            @endif

            @if(session('usuario_rol') == 'Usuario Normal')
                <div class="nav-section-title">Mis Trámites</div>
                <div class="nav-item">
                    <a href="{{ route('tickets.create') }}" class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Crear Ticket</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tickets.mis-tickets') }}" class="nav-link {{ request()->routeIs('tickets.mis-tickets') ? 'active' : '' }}">
                        <i class="bi bi-ticket-detailed"></i>
                        <span>Mis Tickets</span>
                    </a>
                </div>
            @endif
            
            <div class="nav-section-title">Cuenta</div>
            <div class="nav-item">
                <a href="{{ route('perfil') }}" class="nav-link {{ request()->routeIs('perfil') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Mi Perfil</span>
                </a>
            </div>
        </nav>
    </div>
    
    @php
        // ── Auto-breadcrumbs ──────────────────────────────────────────
        $routeName   = Route::currentRouteName() ?? '';
        $rolUsuario  = session('usuario_rol', '');
        $crumbs      = []; // cada item: ['label'=>..., 'url'=>..., 'icon'=>...]

        // Inicio siempre
        $crumbs[] = ['label' => 'Inicio', 'url' => route('dashboard'), 'icon' => 'bi-house-door'];

        // Mapa de rutas → breadcrumbs
        if (str_starts_with($routeName, 'usuarios.') || str_starts_with($routeName, 'admin.usuarios.')) {
            $crumbs[] = ['label' => 'Usuarios', 'url' => route('usuarios.index'), 'icon' => 'bi-people'];
            if (in_array($routeName, ['admin.usuarios.create','admin.usuarios.store']))
                $crumbs[] = ['label' => 'Nuevo Usuario', 'url' => null, 'icon' => null];
            elseif ($routeName === 'usuarios.edit' || $routeName === 'usuarios.update')
                $crumbs[] = ['label' => 'Editar', 'url' => null, 'icon' => null];
            elseif ($routeName === 'usuarios.show')
                $crumbs[] = ['label' => 'Detalle', 'url' => null, 'icon' => null];
            elseif (str_contains($routeName, 'import'))
                $crumbs[] = ['label' => 'Importar CSV', 'url' => null, 'icon' => null];
        } elseif (str_starts_with($routeName, 'admin.tecnicos.')) {
            $crumbs[] = ['label' => 'Usuarios', 'url' => route('usuarios.index'), 'icon' => 'bi-people'];
            $crumbs[] = ['label' => 'Nuevo Técnico', 'url' => null, 'icon' => null];
        } elseif ($routeName === 'tickets.index') {
            $crumbs[] = ['label' => 'Gestión de Tickets', 'url' => null, 'icon' => 'bi-ticket'];
        } elseif ($routeName === 'tickets.create') {
            $crumbs[] = ['label' => 'Crear Ticket', 'url' => null, 'icon' => 'bi-plus-circle'];
        } elseif ($routeName === 'tickets.show') {
            $crumbs[] = ['label' => 'Tickets', 'url' => $rolUsuario === 'Administrador' ? route('tickets.index') : route('tickets.mis-tickets'), 'icon' => 'bi-ticket'];
            $crumbs[] = ['label' => 'Detalle', 'url' => null, 'icon' => null];
        } elseif ($routeName === 'tickets.edit') {
            $crumbs[] = ['label' => 'Tickets', 'url' => $rolUsuario === 'Administrador' ? route('tickets.index') : route('tickets.mis-tickets'), 'icon' => 'bi-ticket'];
            $crumbs[] = ['label' => 'Editar', 'url' => null, 'icon' => null];
        } elseif ($routeName === 'tickets.mis-tickets') {
            $crumbs[] = ['label' => 'Mis Tickets', 'url' => null, 'icon' => 'bi-ticket-detailed'];
        } elseif ($routeName === 'tickets.asignados') {
            $crumbs[] = ['label' => 'Tickets Asignados', 'url' => null, 'icon' => 'bi-list-task'];
        } elseif ($routeName === 'tickets.historial') {
            $crumbs[] = ['label' => 'Historial', 'url' => null, 'icon' => 'bi-clock-history'];
        } elseif ($routeName === 'tecnicos.ver-ticket') {
            $crumbs[] = ['label' => 'Historial', 'url' => route('tickets.historial'), 'icon' => 'bi-clock-history'];
            $crumbs[] = ['label' => 'Ficha Técnica', 'url' => null, 'icon' => null];
        } elseif ($routeName === 'perfil') {
            $crumbs[] = ['label' => 'Mi Perfil', 'url' => null, 'icon' => 'bi-person-circle'];
        } elseif (str_starts_with($routeName, 'reportes.')) {
            $crumbs[] = ['label' => 'Estadísticas', 'url' => route('reportes.index'), 'icon' => 'bi-bar-chart-line'];
            if ($routeName === 'reportes.por-fecha')
                $crumbs[] = ['label' => 'Por Fecha', 'url' => null, 'icon' => null];
            elseif ($routeName === 'reportes.rendimiento')
                $crumbs[] = ['label' => 'Rendimiento', 'url' => null, 'icon' => null];
            elseif ($routeName === 'reportes.encuestas')
                $crumbs[] = ['label' => 'Encuestas', 'url' => null, 'icon' => null];
        } elseif ($routeName === 'papelera.index') {
            $crumbs[] = ['label' => 'Papelera', 'url' => null, 'icon' => 'bi-trash3'];
        }
        // dashboard queda solo con "Inicio"

        // Rol badge config
        $rolBadges = [
            'Administrador'  => ['bg' => '#1e3a5f', 'icon' => 'bi-shield-fill', 'gradient' => 'linear-gradient(135deg, #1e3a5f, #1d4ed8)'],
            'Técnico'        => ['bg' => '#0369a1', 'icon' => 'bi-tools',       'gradient' => 'linear-gradient(135deg, #0369a1, #0ea5e9)'],
            'Usuario Normal' => ['bg' => '#0891b2', 'icon' => 'bi-person-fill', 'gradient' => 'linear-gradient(135deg, #0891b2, #06b6d4)'],
        ];
        $badge = $rolBadges[$rolUsuario] ?? $rolBadges['Usuario Normal'];
    @endphp

    <header class="main-header">
        <div class="d-flex align-items-center">
            <button class="btn btn-link d-lg-none text-dark p-0 me-3" id="btnToggleSidebar" type="button">
                <i class="bi bi-list" style="font-size: 2rem;"></i>
            </button>

            {{-- Breadcrumbs --}}
            <nav class="header-breadcrumbs d-none d-md-flex">
                @foreach($crumbs as $i => $crumb)
                    @if($i > 0)
                        <span class="bc-sep"><i class="bi bi-chevron-right"></i></span>
                    @endif
                    @if($crumb['url'] && $i < count($crumbs) - 1)
                        <a href="{{ $crumb['url'] }}" class="bc-link">
                            @if($crumb['icon'])<i class="{{ $crumb['icon'] }}"></i>@endif
                            {{ $crumb['label'] }}
                        </a>
                    @else
                        <span class="bc-current">
                            @if($crumb['icon'])<i class="{{ $crumb['icon'] }}"></i>@endif
                            {{ $crumb['label'] }}
                        </span>
                    @endif
                @endforeach
            </nav>

            {{-- Mobile: solo último crumb --}}
            <span class="d-md-none fw-bold text-dark" style="font-size:.9rem;">
                @if(!empty($crumbs))
                    @php $last = end($crumbs); @endphp
                    @if($last['icon'])<i class="{{ $last['icon'] }} me-1"></i>@endif{{ $last['label'] }}
                @endif
            </span>
        </div>
        
        <div class="user-menu dropdown">
            <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="cursor:pointer;">
                <div class="hdr-avatar" style="background:{{ $badge['gradient'] }};">
                    {{ strtoupper(substr(session('usuario_nombre','U'), 0, 1)) }}
                </div>
                <div class="d-none d-sm-block" style="line-height:1.15;">
                    <span style="font-weight:600; font-size:.88rem; color:#1e293b;">{{ session('usuario_nombre') }}</span>
                    <span class="d-block" style="font-size:.7rem; font-weight:600; color:{{ $badge['bg'] }};">{{ $rolUsuario }}</span>
                </div>
                <i class="bi bi-chevron-down" style="font-size:.7rem; color:#94a3b8;"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width:220px; border-radius:12px; padding:.5rem;">
                <li style="padding:.6rem .9rem .5rem; border-bottom:1px solid #f1f5f9;">
                    <div style="font-weight:700; font-size:.9rem; color:#1e293b;">{{ session('usuario_nombre') }} {{ session('usuario_apellido','') }}</div>
                    <span class="d-inline-flex align-items-center gap-1 mt-1" style="font-size:.72rem; font-weight:600; color:{{ $badge['bg'] }}; background:{{ $badge['bg'] }}18; padding:.2rem .6rem; border-radius:20px;">
                        <i class="{{ $badge['icon'] }}"></i> {{ $rolUsuario }}
                    </span>
                </li>
                <li class="mt-1">
                    <a class="dropdown-item rounded-2 d-flex align-items-center gap-2" href="{{ route('perfil') }}" style="padding:.55rem .9rem; font-size:.88rem;">
                        <i class="bi bi-person-circle" style="font-size:1.05rem; color:#4f46e5;"></i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 text-danger" href="{{ route('logout.get') }}" style="padding:.55rem .9rem; font-size:.88rem;">
                        <i class="bi bi-box-arrow-right" style="font-size:1.05rem;"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container-fluid px-4 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm fw-bold" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-alert="close" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm fw-bold" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-alert="close" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <ul class="mb-0 fw-bold">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-x-circle me-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-alert="close" aria-label="Close"></button>
                </div>
            @endif
        </div>
        @yield('content')
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnToggle = document.getElementById('btnToggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (btnToggle) {
                btnToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>