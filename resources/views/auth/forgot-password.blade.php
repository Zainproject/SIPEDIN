<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Hubungi Admin - SIPEDIN</title>

    <!-- Custom fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .bg-gradient-primary {
            background:
                radial-gradient(1000px 500px at 20% 10%, rgba(43, 182, 115, .25), transparent 60%),
                radial-gradient(900px 500px at 80% 90%, rgba(20, 122, 82, .25), transparent 55%),
                linear-gradient(135deg, #0b3d2e, #1f9d62);
        }

        .help-card {
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 28px 80px rgba(0, 0, 0, .22);
        }

        /* Gambar bisa tetap pakai yang ada, tapi judul class dinetralkan */
        .bg-help-image {
            background:
                linear-gradient(135deg, rgba(11, 61, 46, .90), rgba(43, 182, 115, .78)),
                url("{{ asset('img/agri-bg.jpg') }}");
            background-size: cover;
            background-position: center;
        }

        .brand-logo {
            width: 86px;
            height: 86px;
            object-fit: contain;
            border-radius: 18px;
            background: #fff;
            padding: 10px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, .12);
            border: 1px solid rgba(0, 0, 0, .06);
        }

        .contact-box {
            border: 1px solid #e7eaf0;
            border-radius: 16px;
            padding: 14px 14px;
            background: #fbfcfe;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px dashed #e7eaf0;
        }

        .contact-item:last-child {
            border-bottom: none;
        }

        .icon-circle {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(43, 182, 115, .12);
            color: #147a52;
        }

        .btn-siped {
            background: linear-gradient(135deg, #147a52, #2bb673);
            border: none;
            border-radius: 14px;
            font-weight: 800;
            padding: .9rem 1rem;
        }

        .btn-siped:hover {
            filter: brightness(.98);
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-xl-10 col-lg-11 col-md-11 col-sm-12 col-12">

                <div class="card help-card border-0">
                    <div class="card-body p-0">
                        <div class="row no-gutters">

                            <!-- LEFT IMAGE -->
                            <div class="col-lg-6 d-none d-lg-block bg-help-image"></div>

                            <!-- RIGHT CONTENT -->
                            <div class="col-lg-6 bg-white">
                                <div class="p-5">

                                    <div class="text-center mb-4">
                                        <img src="{{ asset('img/sumenep.png') }}" class="brand-logo mb-3"
                                            alt="Logo Instansi">
                                        <h1 class="h4 text-gray-900 font-weight-bold mb-1">Lupa Kata Sandi?</h1>
                                        <p class="small text-muted mb-0">
                                            Demi keamanan, pengaturan ulang kata sandi hanya dapat dilakukan oleh Admin
                                            Sistem.
                                        </p>
                                    </div>

                                    <div class="contact-box mb-3">
                                        <div class="contact-item">
                                            <span class="icon-circle"><i class="fab fa-whatsapp"></i></span>
                                            <div>
                                                <div class="small text-muted">WhatsApp Admin</div>
                                                <div class="font-weight-bold">+62 831-6400-4093</div>
                                            </div>
                                        </div>

                                        <div class="contact-item">
                                            <span class="icon-circle"><i class="fas fa-phone-alt"></i></span>
                                            <div>
                                                <div class="small text-muted">Telepon Kantor</div>
                                                <div class="font-weight-bold">+62 831-6400-4093</div>
                                            </div>
                                        </div>

                                        <div class="contact-item">
                                            <span class="icon-circle"><i class="fas fa-user-shield"></i></span>
                                            <div>
                                                <div class="small text-muted">Dukungan Teknis</div>
                                                <div class="font-weight-bold">Tim Pengelola SIPEDIN</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="small text-muted mb-4">
                                        <strong>Catatan:</strong> Sertakan nama lengkap, unit kerja, serta
                                        email/username saat menghubungi admin.
                                    </div>

                                    <!-- ACTION BUTTONS -->
                                    <a class="btn btn-siped btn-block text-white mb-2"
                                        href="https://wa.me/6283164004093" target="_blank" rel="noopener">
                                        <i class="fab fa-whatsapp mr-2"></i> Hubungi Admin via WhatsApp
                                    </a>

                                    <a class="btn btn-outline-secondary btn-block" href="{{ route('login') }}">
                                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Login
                                    </a>

                                    <hr>

                                    <div class="text-center small text-muted">
                                        © {{ date('Y') }} SIPEDIN • Instansi/Unit Kerja
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

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
