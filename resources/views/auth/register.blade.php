<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register - SIPEDIN</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --siped-900: #0b3d2e;
            --siped-800: #0f5a3f;
            --siped-700: #147a52;
            --siped-600: #1f9d62;
            --siped-500: #2bb673;
            --soft: #f6fbf8;
        }

        body {
            min-height: 100vh;
            font-family: "Nunito", sans-serif;
        }

        .bg-gradient-primary {
            background:
                radial-gradient(1000px 520px at 20% 10%, rgba(43, 182, 115, .22), transparent 60%),
                radial-gradient(900px 520px at 85% 90%, rgba(15, 90, 63, .18), transparent 55%),
                linear-gradient(135deg, var(--siped-900), var(--siped-600));
        }

        .register-card {
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 28px 80px rgba(0, 0, 0, .22);
            border: 1px solid rgba(255, 255, 255, .10);
        }

        /* Panel kiri (gambar) */
        .bg-register-image {
            background:
                linear-gradient(135deg, rgba(11, 61, 46, .88), rgba(43, 182, 115, .70)),
                url("{{ asset('img/agri-bg.jpg') }}");
            background-size: cover;
            background-position: center;
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .45rem .85rem;
            border-radius: 999px;
            background: rgba(43, 182, 115, .12);
            color: var(--siped-700);
            font-weight: 800;
        }

        .page-title {
            font-weight: 900;
            letter-spacing: .2px;
        }

        /* Box section */
        .section-box {
            background: var(--soft);
            border: 1px solid #e6f3ea;
            border-radius: 16px;
            padding: 16px;
            margin-top: 14px;
        }

        .section-title {
            font-weight: 900;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .section-title .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--siped-500);
            display: inline-block;
        }

        /* Input */
        .form-control-user {
            border-radius: 14px !important;
            padding: 1rem 1rem;
            border: 1px solid #e7eaf0;
            background: #fff;
        }

        .form-control-user:focus,
        .form-control:focus {
            border-color: rgba(43, 182, 115, .55);
            box-shadow: 0 0 0 .2rem rgba(43, 182, 115, .18);
        }

        /* file input */
        .form-control[type="file"] {
            border-radius: 14px;
        }

        /* Tombol */
        .btn-siped {
            background: linear-gradient(135deg, var(--siped-700), var(--siped-500));
            border: none;
            border-radius: 14px;
            font-weight: 900;
            padding: .95rem 1rem;
            box-shadow: 0 12px 28px rgba(31, 157, 98, .20);
        }

        .btn-siped:hover {
            filter: brightness(.98);
            box-shadow: 0 14px 32px rgba(31, 157, 98, .26);
        }

        /* Lebar & spacing */
        .container {
            padding-top: 26px;
            padding-bottom: 26px;
        }

        @media (max-width: 991.98px) {
            .p-5 {
                padding: 2.2rem !important;
            }
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">

        <div class="card register-card border-0 my-4">
            <div class="card-body p-0">
                <div class="row no-gutters">

                    <!-- LEFT IMAGE -->
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>

                    <!-- RIGHT FORM -->
                    <div class="col-lg-7 bg-white">
                        <div class="p-5">

                            <div class="text-center mb-3">
                                <div class="header-badge mb-2">
                                    <i class="fas fa-file-signature"></i> Aplikasi Persuratan & Perintah Dinas
                                </div>
                                <h1 class="h4 text-gray-900 mb-2 page-title">Pendaftaran Akun</h1>
                                <div class="small text-muted">SIPEDIN • Sistem Informasi Perintah Dinas</div>
                            </div>

                            {{-- Error umum --}}
                            @if ($errors->any())
                                <div class="alert alert-danger small">
                                    <div class="font-weight-bold mb-1">Terjadi kesalahan:</div>
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="user" method="POST" action="{{ route('register') }}"
                                enctype="multipart/form-data">
                                @csrf

                                {{-- ROLE default (kalau kamu pakai kolom role) --}}
                                <input type="hidden" name="role" value="pejabat">

                                <!-- AVATAR -->
                                <div class="section-box">
                                    <div class="section-title text-gray-900">
                                        <span class="dot"></span> Foto (Avatar)
                                        <span class="text-muted font-weight-normal">(opsional)</span>
                                    </div>

                                    <div class="form-group mb-2">
                                        <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                            name="avatar" accept="image/*">
                                        <small class="text-muted">JPG/PNG/WebP. Maks 2MB.</small>
                                        @error('avatar')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- DATA PEJABAT -->
                                <div class="section-box">
                                    <div class="section-title text-primary">
                                        <span class="dot"></span> Data Pejabat (Tanda Tangan Pejabat)
                                    </div>

                                    <div class="form-group">
                                        <input type="text"
                                            class="form-control form-control-user @error('name') is-invalid @enderror"
                                            name="name" value="{{ old('name') }}" placeholder="Nama Pejabat"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="text"
                                            class="form-control form-control-user @error('nip') is-invalid @enderror"
                                            name="nip" value="{{ old('nip') }}" placeholder="NIP Pejabat"
                                            required>
                                        @error('nip')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="text"
                                                class="form-control form-control-user @error('pangkat') is-invalid @enderror"
                                                name="pangkat" value="{{ old('pangkat') }}"
                                                placeholder="Pangkat (opsional)">
                                            @error('pangkat')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text"
                                                class="form-control form-control-user @error('masa_bakti') is-invalid @enderror"
                                                name="masa_bakti" value="{{ old('masa_bakti') }}"
                                                placeholder="Masa Bakti (opsional)">
                                            @error('masa_bakti')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <input type="text"
                                            class="form-control form-control-user @error('jabatan') is-invalid @enderror"
                                            name="jabatan" value="{{ old('jabatan') }}"
                                            placeholder="Jabatan (opsional)">
                                        @error('jabatan')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- DATA BENDAHARA -->
                                <div class="section-box">
                                    <div class="section-title text-success">
                                        <span class="dot"></span> Data Bendahara (Tanda Tangan Bendahara)
                                    </div>

                                    <div class="form-group">
                                        <input type="text"
                                            class="form-control form-control-user @error('bendahara_nama') is-invalid @enderror"
                                            name="bendahara_nama" value="{{ old('bendahara_nama') }}"
                                            placeholder="Nama Bendahara" required>
                                        @error('bendahara_nama')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="text"
                                            class="form-control form-control-user @error('bendahara_nip') is-invalid @enderror"
                                            name="bendahara_nip" value="{{ old('bendahara_nip') }}"
                                            placeholder="NIP Bendahara" required>
                                        @error('bendahara_nip')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="text"
                                                class="form-control form-control-user @error('bendahara_pangkat') is-invalid @enderror"
                                                name="bendahara_pangkat" value="{{ old('bendahara_pangkat') }}"
                                                placeholder="Pangkat (opsional)">
                                            @error('bendahara_pangkat')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text"
                                                class="form-control form-control-user @error('bendahara_masa_bakti') is-invalid @enderror"
                                                name="bendahara_masa_bakti" value="{{ old('bendahara_masa_bakti') }}"
                                                placeholder="Masa Bakti (opsional)">
                                            @error('bendahara_masa_bakti')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <input type="text"
                                            class="form-control form-control-user @error('bendahara_jabatan') is-invalid @enderror"
                                            name="bendahara_jabatan" value="{{ old('bendahara_jabatan') }}"
                                            placeholder="Jabatan (opsional)">
                                        @error('bendahara_jabatan')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- DATA LOGIN -->
                                <div class="section-box">
                                    <div class="section-title text-dark">
                                        <span class="dot"></span> Data Login
                                    </div>

                                    <div class="form-group">
                                        <input type="email"
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}"
                                            placeholder="Email (contoh: nama@instansi.go.id)" required>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="password"
                                                class="form-control form-control-user @error('password') is-invalid @enderror"
                                                name="password" placeholder="Kata Sandi" required>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control form-control-user"
                                                name="password_confirmation" placeholder="Ulangi Kata Sandi" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-siped btn-block text-white mt-4">
                                    <i class="fas fa-user-plus mr-2"></i> Daftarkan Akun
                                </button>
                            </form>

                            <hr>

                            <div class="text-center">
                                <a class="small" href="{{ route('login') }}">Sudah memiliki akun? Masuk</a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>

</html>
