<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login Billing RSA</title>

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition login-page">

    <div class="login-box">

        <div class="login-logo">
            <img src="{{ asset('vendor/adminlte/dist/img/AdminLTELogo.png') }}" width="70">
            <br>
            <b>Billing</b> RSA
        </div>

        <div class="card">
            <div class="card-body login-card-body">

                <p class="login-box-msg">Silakan login untuk melanjutkan</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="text" name="Username" class="form-control" placeholder="Username" required
                            autofocus>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-info btn-block">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        Login
                    </button>

                </form>

            </div>
        </div>

    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

</body>

</html>
