@extends('index')
@section('title', 'Profil - SIPEDIN')

@section('main')
    @php($user = auth()->user())

    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Profil</h1>
        </div>

        @if (!$user)
            <div class="alert alert-warning">
                Sesi login tidak ditemukan. Silakan login kembali.
            </div>
        @else
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

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

            <style>
                .section-box {
                    background: #f6fbf8;
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
                    background: #2bb673;
                    display: inline-block;
                }

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

                .form-control[type="file"] {
                    border-radius: 14px;
                }

                .btn-siped {
                    background: linear-gradient(135deg, #147a52, #2bb673);
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

                .brand-avatar {
                    width: 90px;
                    height: 90px;
                    object-fit: cover;
                    border-radius: 18px;
                    background: #fff;
                    padding: 6px;
                    border: 1px solid rgba(0, 0, 0, .06);
                    box-shadow: 0 12px 25px rgba(0, 0, 0, .12);
                }
            </style>

            <div class="row">

                <!-- LEFT: UPDATE PROFIL (sesuai register: avatar + pejabat + bendahara + login) -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Perbarui Profil</h6>
                        </div>

                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img class="brand-avatar mb-2"
                                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('img/undraw_profile.svg') }}"
                                    alt="Avatar">
                                <div class="small text-muted">SIPEDIN • Sistem Informasi Perintah Dinas</div>
                            </div>

                            <form class="user" method="POST" action="{{ route('profile.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

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
                                            name="name" value="{{ old('name', $user->name) }}" placeholder="Nama Pejabat"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="text"
                                            class="form-control form-control-user @error('nip') is-invalid @enderror"
                                            name="nip" value="{{ old('nip', $user->nip) }}" placeholder="NIP Pejabat"
                                            required>
                                        @error('nip')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="text"
                                                class="form-control form-control-user @error('pangkat') is-invalid @enderror"
                                                name="pangkat" value="{{ old('pangkat', $user->pangkat) }}"
                                                placeholder="Pangkat (opsional)">
                                            @error('pangkat')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text"
                                                class="form-control form-control-user @error('masa_bakti') is-invalid @enderror"
                                                name="masa_bakti" value="{{ old('masa_bakti', $user->masa_bakti) }}"
                                                placeholder="Masa Bakti (opsional)">
                                            @error('masa_bakti')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <input type="text"
                                            class="form-control form-control-user @error('jabatan') is-invalid @enderror"
                                            name="jabatan" value="{{ old('jabatan', $user->jabatan) }}"
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
                                            name="bendahara_nama"
                                            value="{{ old('bendahara_nama', $user->bendahara_nama) }}"
                                            placeholder="Nama Bendahara" required>
                                        @error('bendahara_nama')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="text"
                                            class="form-control form-control-user @error('bendahara_nip') is-invalid @enderror"
                                            name="bendahara_nip" value="{{ old('bendahara_nip', $user->bendahara_nip) }}"
                                            placeholder="NIP Bendahara" required>
                                        @error('bendahara_nip')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="text"
                                                class="form-control form-control-user @error('bendahara_pangkat') is-invalid @enderror"
                                                name="bendahara_pangkat"
                                                value="{{ old('bendahara_pangkat', $user->bendahara_pangkat) }}"
                                                placeholder="Pangkat (opsional)">
                                            @error('bendahara_pangkat')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text"
                                                class="form-control form-control-user @error('bendahara_masa_bakti') is-invalid @enderror"
                                                name="bendahara_masa_bakti"
                                                value="{{ old('bendahara_masa_bakti', $user->bendahara_masa_bakti) }}"
                                                placeholder="Masa Bakti (opsional)">
                                            @error('bendahara_masa_bakti')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <input type="text"
                                            class="form-control form-control-user @error('bendahara_jabatan') is-invalid @enderror"
                                            name="bendahara_jabatan"
                                            value="{{ old('bendahara_jabatan', $user->bendahara_jabatan) }}"
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

                                    <div class="form-group mb-0">
                                        <input type="email"
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email', $user->email) }}"
                                            placeholder="Email (contoh: nama@instansi.go.id)" required>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-siped btn-block text-white mt-3">
                                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: UPDATE PASSWORD -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Ubah Kata Sandi</h6>
                        </div>
                        <div class="card-body">
                            <form class="user" method="POST" action="{{ route('profile.password') }}">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label class="small font-weight-bold text-gray-700">Kata Sandi Saat Ini</label>
                                    <input type="password"
                                        class="form-control form-control-user @error('current_password') is-invalid @enderror"
                                        name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="small font-weight-bold text-gray-700">Kata Sandi Baru</label>
                                    <input type="password"
                                        class="form-control form-control-user @error('password') is-invalid @enderror"
                                        name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="small font-weight-bold text-gray-700">Ulangi Kata Sandi Baru</label>
                                    <input type="password" class="form-control form-control-user"
                                        name="password_confirmation" required>
                                </div>

                                <button class="btn btn-primary btn-block">
                                    <i class="fas fa-key mr-2"></i> Perbarui Kata Sandi
                                </button>

                                <div class="small text-muted mt-3">
                                    Gunakan kata sandi minimal 8 karakter.
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        @endif

    </div>
@endsection
