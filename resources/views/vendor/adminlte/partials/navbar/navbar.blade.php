@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav
    class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">

        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Right sidebar toggler link --}}
        @if ($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif

        {{-- User --}}
        <li class="nav-item dropdown">

            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user-circle"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                <span class="dropdown-item dropdown-header">
                    {{ Auth::user()->Nama ?? 'User' }}
                </span>

                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item"
                    onclick="event.preventDefault(); $('.dropdown-menu').removeClass('show'); $('#modalGantiPassword').modal('show');">
                    <i class="fas fa-user-edit mr-2"></i>
                    Update Profile
                </a>


                <div class="dropdown-divider"></div>

                <a href="{{ route('logout') }}" class="dropdown-item text-center"
                    onclick="event.preventDefault();
               document.getElementById('logout-form').submit();">

                    <i class="fas fa-power-off mr-1"></i>
                    Logout

                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>

            </div>


        </li>

    </ul>

</nav>

</nav>

@if (session('success'))
    <div id="successAlert" class="alert alert-success alert-dismissible fade show"
        style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px;">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>

    <script>
        setTimeout(function() {
            $('#successAlert').fadeOut('slow');
        }, 3000);
    </script>
@endif

@if ($errors->any())
    <div id="errorAlert" class="alert alert-danger alert-dismissible fade show"
        style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px;">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>

    <script>
        setTimeout(function() {
            $('#errorAlert').fadeOut('slow');
        }, 3000);
    </script>
@endif

{{-- Modal Ganti Password --}}
<div class="modal fade" id="modalGantiPassword" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-key mr-2"></i>
                    Ganti Password
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ Auth::user()->ID }}">

                @if ($errors->any())
                    <div class="alert alert-danger m-2">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="modal-body">

                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" class="form-control form-control-sm" required>
                    </div>

                    <div class="form-group mb-0">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_baru_confirmation" class="form-control form-control-sm"
                            required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-info btn-sm">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<script>
    $('#modalGantiPassword').on('shown.bs.modal', function() {
        $('.dropdown-menu').removeClass('show');
    });
</script>
