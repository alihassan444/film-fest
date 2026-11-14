<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.favicon')
    <link href="{{ asset('frontend/css/developer-credit.css') }}?v={{ filemtime(public_path('frontend/css/developer-credit.css')) }}" rel="stylesheet">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Admin') · {{ config('app.name', 'Film Fest') }}</title>
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/brand.css') }}" rel="stylesheet">
    @stack('styles')
    <link href="{{ asset('admin/css/workspace.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/motion.css') }}?v={{ filemtime(public_path('frontend/css/motion.css')) }}" rel="stylesheet">
    <script src="{{ asset('frontend/js/motion.js') }}?v={{ filemtime(public_path('frontend/js/motion.js')) }}" defer></script>
</head>
<body class="admin-body">
    @php
        $pendingSidebarCount = \App\Models\Ticket::where('status', 'pending')->count();
        $userName = Auth::user()->name ?? 'Administrator';
    @endphp

    <aside class="admin-sidebar" id="adminSidebar" aria-label="Admin navigation">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">
            <span class="festival-logo"><img src="{{ asset('frontend/images/filmfestlogo.png') }}" alt="IAC Film Festival logo"></span>
            <span>
                <span class="admin-brand-name">Film Fest-26</span>
                <span class="admin-brand-kicker">ADMIN WORKSPACE</span>
            </span>
        </a>

        <div class="admin-nav-label">Workspace</div>
        <nav class="admin-nav">
            <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid"></i><span>Dashboard</span>
            </a>
            <a class="admin-nav-link {{ request()->routeIs('admin.tickets.index', 'admin.tickets.show') && !request('status') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
                <i class="bi bi-ticket-perforated"></i><span>All tickets</span>
            </a>
            <a class="admin-nav-link {{ request()->routeIs('admin.tickets.pending') ? 'active' : '' }}" href="{{ route('admin.tickets.pending') }}">
                <i class="bi bi-clock"></i><span>Pending tickets</span>
                @if($pendingSidebarCount > 0)<span class="admin-nav-count">{{ $pendingSidebarCount }}</span>@endif
            </a>
            <a class="admin-nav-link {{ request()->routeIs('admin.tickets.index') && request('status') === 'verified' ? 'active' : '' }}" href="{{ route('admin.tickets.index', ['status' => 'verified']) }}">
                <i class="bi bi-patch-check"></i><span>Verified tickets</span>
            </a>
            <a class="admin-nav-link {{ request()->routeIs('admin.tickets.rejected') ? 'active' : '' }}" href="{{ route('admin.tickets.rejected') }}">
                <i class="bi bi-x-circle"></i><span>Rejected tickets</span>
            </a>
            <a class="admin-nav-link {{ request()->routeIs('admin.attendance', 'admin.markAttendance', 'admin.saveNote', 'admin.updateNote', 'admin.deleteNote') ? 'active' : '' }}" href="{{ route('admin.attendance') }}">
                <i class="bi bi-journal-check"></i><span>Attendance & notes</span>
            </a>
            <a class="admin-nav-link {{ request()->routeIs('admin.students.*', 'admin.storeStudent', 'admin.deleteStudent') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                <i class="bi bi-people"></i><span>Students</span>
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            <div class="admin-user">
                <img class="admin-avatar" src="{{ asset('admin/images/graphical-avatar.png') }}" alt="Administrator avatar" width="38" height="38">
                <div class="admin-user-details">
                    <div class="admin-user-name" title="{{ $userName }}">{{ $userName }}</div>
                    <div class="admin-user-role">Administrator</div>
                </div>
            </div>
            <form class="admin-logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="admin-logout" type="submit" aria-label="Log out" title="Log out"><i class="bi bi-box-arrow-right" aria-hidden="true"></i></button>
            </form>
        </div>
    </aside>
    <div class="admin-overlay" id="adminOverlay"></div>

    <div class="admin-shell">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="admin-mobile-toggle" id="adminMenuButton" type="button" aria-label="Open navigation">
                    <i class="bi bi-list"></i>
                </button>
                <div class="admin-breadcrumb">
                    <span>Workspace</span><i class="bi bi-chevron-right"></i><strong>@yield('page-title', 'Dashboard')</strong>
                </div>
            </div>
            <div class="admin-topbar-meta">
                <span><i class="bi bi-calendar3 me-2"></i>{{ now()->format('l, d F Y') }}</span>
                <span class="admin-role-badge"><i class="bi bi-shield-check me-2"></i>Admin panel</span>
            </div>
        </header>

        <main class="admin-content">
            @if(session('success'))
                <div class="admin-alert admin-alert-success" role="status"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="admin-alert admin-alert-danger" role="alert"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="admin-alert admin-alert-danger">
                    <strong>Please check the form:</strong>
                    <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')

            <footer class="admin-footer">
                <span>Film Fest • Administration</span>
                <span>@include('partials.developer-credit')</span>
            </footer>
        </main>
    </div>

    <div class="modal fade" id="receiptPreviewModal" tabindex="-1" aria-labelledby="receiptPreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div><h2 class="modal-title" id="receiptPreviewTitle">Payment receipt</h2><p class="admin-card-copy mt-1 mb-0" id="receiptPreviewReference"></p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close receipt preview"></button>
                </div>
                <div class="modal-body receipt-preview-body">
                    <p id="receiptPreviewStatus" role="status">Loading receipt…</p>
                    <img id="receiptPreviewImage" alt="Payment receipt" hidden>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close preview</button></div>
            </div>
        </div>
    </div>
    <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/js/receipt-preview.js') }}"></script>
    <script>
        (() => {
            // Keep dialogs outside scrollable tables and card stacking contexts.
            window.prepareAdminDialogs = () => {
                document.querySelectorAll('.admin-content .modal').forEach(modal => {
                    modal.dataset.ticketDialog = 'true';
                    document.body.appendChild(modal);
                });
            };
            window.prepareAdminDialogs();

            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminOverlay');
            const toggle = document.getElementById('adminMenuButton');
            const closeMenu = () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            };
            toggle?.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            });
            overlay?.addEventListener('click', closeMenu);
            document.addEventListener('keydown', event => event.key === 'Escape' && closeMenu());
        })();
    </script>
    @stack('scripts')
</body>
</html>
