<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Aira Admin</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/adminlte/css/adminlte.min.css') }}">
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" id="notificationsDropdown">
                    <i class="far fa-bell"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge badge-warning navbar-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">{{ auth()->user()->notifications->count() }} Notifikasi</span>
                    <div class="dropdown-divider"></div>
                    
                    @forelse(auth()->user()->notifications->take(5) as $notification)
                        <a href="#" class="dropdown-item {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div class="text-wrap">
                                <strong>{{ $notification->data['type'] ?? 'Notification' }}</strong>
                                <p class="text-sm mb-0">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-muted text-sm mb-0">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                    @empty
                        <span class="dropdown-item text-muted">Tidak ada notifikasi</span>
                        <div class="dropdown-divider"></div>
                    @endforelse

                    <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
                </div>
            </li>

            <!-- User Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('admin.logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                       class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <span class="brand-text font-weight-light">Aira Admin</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.streaming.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.streaming.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-video"></i>
                            <p>
                                Live Streaming
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.streaming.index') }}" 
                                   class="nav-link {{ request()->routeIs('admin.streaming.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Dashboard Live</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.streaming.vouchers.index') }}" 
                                   class="nav-link {{ request()->routeIs('admin.streaming.vouchers.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Voucher Live</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.streaming.orders.index') }}" 
                                   class="nav-link {{ request()->routeIs('admin.streaming.orders.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pesanan Live</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Products</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Orders</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Payment Confirmations</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.shipping.index') }}" class="nav-link {{ request()->routeIs('admin.shipping.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Shipping</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.payment') }}" class="nav-link {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Payment Settings</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.whatsapp.index') }}" class="nav-link {{ request()->routeIs('admin.whatsapp.*') ? 'active' : '' }}">
                            <i class="nav-icon fab fa-whatsapp"></i>
                            <p>
                                WhatsApp Messages
                                <span class="right badge badge-success" id="unread-whatsapp-count">0</span>
                            </p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; {{ date('Y') }} Aira Store.</strong> All rights reserved.
    </footer>
</div>

<!-- jQuery -->
<script src="{{ asset('assets/adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/adminlte/js/adminlte.min.js') }}"></script>
@stack('scripts')

<script>
// CSRF Token setup for Axios
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function updateWhatsAppCount() {
    axios.get('{{ route("admin.whatsapp.statistics") }}')
        .then(response => {
            const unreadCount = response.data.unread || 0;
            const badge = document.getElementById('unread-whatsapp-count');
            if (badge) {
                badge.textContent = unreadCount;
                badge.style.display = unreadCount > 0 ? 'inline' : 'none';
            }
        })
        .catch(error => console.error('Failed to fetch WhatsApp statistics:', error));
}

// Update count every 30 seconds
setInterval(updateWhatsAppCount, 30000);

// Initial update
document.addEventListener('DOMContentLoaded', updateWhatsAppCount);
</script>
</body>
</html>
