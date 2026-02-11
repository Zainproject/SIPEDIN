<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - SIPEDIN</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin 2 -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --siped-700: #147a52;
            --siped-600: #1f9d62;
            --siped-500: #2bb673;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Nunito", sans-serif;
        }

        /* BACKGROUND */
        .bg-gradient-primary {
            background:
                radial-gradient(1000px 500px at 20% 10%, rgba(43, 182, 115, .25), transparent 60%),
                radial-gradient(900px 500px at 80% 90%, rgba(20, 122, 82, .25), transparent 55%),
                linear-gradient(135deg, #0b3d2e, #1f9d62);
        }

        /* LOGIN CARD */
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 22px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 28px 80px rgba(0, 0, 0, .25);
        }

        @media (min-width: 992px) {
            .login-card {
                max-width: 560px;
            }
        }

        @media (max-width: 575.98px) {
            .login-card {
                max-width: 100%;
                margin: 0 16px;
            }
        }

        /* HEADER */
        .login-header {
            background: linear-gradient(135deg, var(--siped-700), var(--siped-500));
            color: #fff;
            padding: 44px 40px;
            text-align: center;
        }

        @media (max-width: 575.98px) {
            .login-header {
                padding: 36px 24px;
            }
        }

        .login-header .badge {
            background: rgba(255, 255, 255, .22);
            font-weight: 700;
            padding: .45rem .9rem;
            border-radius: 999px;
        }

        .login-header h1 {
            font-weight: 900;
            font-size: 2rem;
            margin-top: 18px;
            margin-bottom: 6px;
            letter-spacing: .6px;
        }

        .login-header p {
            opacity: .9;
            font-size: .95rem;
            margin-bottom: 0;
        }

        /* BODY */
        .login-body {
            padding: 48px 44px 36px;
        }

        @media (max-width: 575.98px) {
            .login-body {
                padding: 36px 28px 28px;
            }
        }

        .brand-logo {
            width: 88px;
            height: 88px;
            object-fit: contain;
            border-radius: 18px;
            background: #fff;
            padding: 10px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, .12);
            border: 1px solid rgba(0, 0, 0, .06);
        }

        .form-control-user {
            border-radius: 14px !important;
            padding: 1rem;
            border: 1px solid #e7eaf0;
            background: #fbfcfe;
        }

        .form-control-user:focus {
            border-color: rgba(43, 182, 115, .6);
            box-shadow: 0 0 0 .2rem rgba(43, 182, 115, .2);
            background: #fff;
        }

        .btn-user {
            border-radius: 14px !important;
            padding: .95rem;
            font-weight: 800;
        }

        .btn-siped {
            background: linear-gradient(135deg, var(--siped-700), var(--siped-500));
            border: none;
        }

        .btn-siped:hover {
            filter: brightness(.97);
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="login-card">

        <!-- HEADER -->
        <div class="login-header">
            <span class="badge">
                <i class="fas fa-file-signature mr-1"></i> Aplikasi Persuratan & Perintah Dinas
            </span>
            <h1>SIPEDIN</h1>
            <p>Sistem Informasi Perintah Dinas</p>
        </div>

        <!-- BODY -->
        <div class="login-body text-center">

            <img src="{{ asset('img/sumenep.png') }}" class="brand-logo mb-4" alt="Logo Instansi">

            @if ($errors->any())
                <div class="alert alert-danger small text-left">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success small">
                    {{ session('status') }}
                </div>
            @endif

            <form class="user text-left" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="small font-weight-bold text-gray-700">Email</label>
                    <input type="email" class="form-control form-control-user" name="email"
                        placeholder="nama@instansi.go.id" required>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-gray-700">Kata Sandi</label>
                    <input type="password" class="form-control form-control-user" name="password"
                        placeholder="Masukkan kata sandi" required>
                </div>

                <div class="form-group d-flex justify-content-between align-items-center">
                    <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="remember_me">
                        <label class="custom-control-label" for="remember_me">
                            Ingat saya
                        </label>
                    </div>

                    <a class="small" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                </div>

                <button type="submit" class="btn btn-user btn-block text-white btn-siped">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>

            <hr>

            <div class="small text-muted">
                © {{ date('Y') }} SIPEDIN • Instansi/Unit Kerja
            </div>

        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

</body>

</html>
