@php
    $routeName = Route::currentRouteName();
@endphp

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/images/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">
            {{ config('app.name', 'Laravel') }}
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block text-capitalize">
                    @if (Auth::check())
                        {{ Auth::user()->name }}
                    @endif
                </a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link @if ($routeName == 'dashboard') active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('log.subs-and-unsubs') }}"
                        class="nav-link @if ($routeName == 'log.subs-and-unsubs') active @endif">
                        <i class="fa-solid fa-arrows-turn-to-dots nav-icon"></i>
                        <p>
                            Subs and Unsubs Logs
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('log.charge') }}"
                        class="nav-link @if ($routeName == 'log.charge') active @endif">
                        <i class="fa-solid fa-down-left-and-up-right-to-center nav-icon"></i>
                        <p>
                            Charge Logs
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('log.ondemand.charge') }}"
                        class="nav-link @if ($routeName == 'log.ondemand.charge') active @endif">
                        <i class="fa-solid fa-bolt nav-icon"></i>
                        <p>
                            Ondemand Charge Logs
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('log.subs-based') }}"
                        class="nav-link @if ($routeName == 'log.subs-based') active @endif">
                        <i class="fa-solid fa-dice nav-icon"></i>
                        <p>
                            Subs Based Logs
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('service.index') }}"
                        class="nav-link @if ($routeName == 'service.index') active @endif">
                        <i class="fa-solid fa-list nav-icon"></i>
                        <p>
                            Service List
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('service-provider-info.index') }}"
                        class="nav-link @if ($routeName == 'service-provider-info.index') active @endif">
                        <i class="fa-solid fa-circle-info nav-icon"></i>
                        <p>
                            Provider Info
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('customer-log.index') }}"
                        class="nav-link @if ($routeName == 'customer-log.index') active @endif">
                        <i class="fa-solid fa-business-time nav-icon"></i>
                        <p>
                            Customer Log
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
