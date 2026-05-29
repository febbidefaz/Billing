<aside class="main-sidebar sidebar-dark-info elevation-4">

    <!-- Logo -->
    <a href="/dashboard" class="brand-link">

        <img src="/vendor/adminlte/dist/img/AdminLTELogo.png" alt="RSA" class="brand-image img-circle elevation-3"
            style="opacity:.8">

        <span class="brand-text font-weight-light">
            Billing RSA
        </span>

    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="img-circle elevation-2 d-flex align-items-center justify-content-center"
                style="
               width:35px;
               height:35px;
               background:#17a2b8;
               color:white;
               font-size:18px;
            ">
                <i class="fas fa-hospital-user"></i>
            </div>

            <div class="info">
                <a href="#" class="d-block">
                    {{ auth()->user()->Nama ?? 'Administrator' }}
                </a>
            </div>

        </div>

        <!-- Menu -->
        <nav class="mt-2">

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Rawat Inap -->
                <li class="nav-item">

                    <a href="/rawat-inap" class="nav-link menu-link">

                        <i class="nav-icon fas fa-bed"></i>

                        <p>Rawat Inap</p>

                    </a>

                </li>

                <!-- Pulang -->
                <li class="nav-item">

                    <a href="/rawat-inap" class="nav-link menu-link">

                        <i class="nav-icon fas fa-home"></i>

                        <p>Pulang</p>

                    </a>

                </li>

                <!-- Rawat jalan -->
                <li class="nav-item">

                    <a href="/rawat-jalan" class="nav-link menu-link">
                        <i class="nav-icon fas fa-user-injured"></i>
                        <p>Rawat Jalan</p>
                    </a>

                </li>

                <!-- User Billing -->
                @if (auth()->user()->Role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('userbilling.index') }}" class="nav-link menu-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>User Billing</p>
                        </a>
                    </li>
                @endif


            </ul>

        </nav>

    </div>

</aside>
