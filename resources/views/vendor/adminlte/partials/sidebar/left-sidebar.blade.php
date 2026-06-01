<aside class="main-sidebar sidebar-dark-info elevation-4">

    <a href="{{ route('rawatinap.index') }}" class="brand-link">
        <img src="{{ asset('vendor/adminlte/dist/img/AdminLTELogo.png') }}" alt="RSA"
            class="brand-image img-circle elevation-3" style="opacity:.8">

        <span class="brand-text font-weight-light">
            Billing RSA
        </span>
    </a>

    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="img-circle elevation-2 d-flex align-items-center justify-content-center"
                style="width:35px;height:35px;background:#17a2b8;color:white;font-size:18px;">
                <i class="fas fa-hospital-user"></i>
            </div>

            <div class="info">
                <a href="#" class="d-block">
                    {{ auth()->user()->Nama ?? 'Administrator' }}
                </a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">

                <li class="nav-item">
                    <a href="{{ route('rawatinap.index') }}"
                        class="nav-link {{ request()->routeIs('rawatinap.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bed"></i>
                        <p>Rawat Inap</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('pulang.index') }}"
                        class="nav-link {{ request()->routeIs('pulang.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Pulang</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('igd.index') }}"
                        class="nav-link {{ request()->routeIs('igd.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ambulance"></i>
                        <p>IGD</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('rawatjalan.index') }}"
                        class="nav-link {{ request()->routeIs('rawatjalan.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-injured"></i>
                        <p>Rawat Jalan</p>
                    </a>
                </li>

                @if (auth()->user() && auth()->user()->Role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('userbilling.index') }}"
                            class="nav-link {{ request()->routeIs('userbilling.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>User Billing</p>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>

    </div>

</aside>
