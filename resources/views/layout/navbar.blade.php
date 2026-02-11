{{-- ✅ THEME OVERRIDE (PERTANIAN) - TANPA MENGUBAH STRUKTUR --}}
<style>
    :root {
        --agri-primary: #0f5a3f;
        /* hijau utama */
        --agri-secondary: #147a52;
        /* hijau lebih terang */
    }

    /* ==============================
       KEMBALIKAN TOAST SELAMAT DATANG
       KE TAMPILAN DEFAULT
       ============================== */

    #welcomeToast.alert-success {
        background-color: #d4edda !important;
        /* default bootstrap */
        color: #155724 !important;
        border-color: #c3e6cb !important;
    }

    #welcomeToast.alert-success .close {
        color: #155724 !important;
        opacity: .8;
    }

    #welcomeToast.alert-success .close:hover {
        opacity: 1;
    }

    .dropdown-header form button:hover {
        text-decoration: underline;
        opacity: .85;
    }

    /* ganti semua yang "primary" (biru) jadi hijau pertanian */
    .bg-primary {
        background-color: var(--agri-primary) !important;
    }

    .btn-primary {
        background-color: var(--agri-primary) !important;
        border-color: var(--agri-primary) !important;
        color: #fff !important;
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background-color: var(--agri-secondary) !important;
        border-color: var(--agri-secondary) !important;
        color: #fff !important;
    }

    .dropdown-header .btn-link:hover {
        opacity: .85;
        text-decoration: underline !important;
    }

    /* HEADER DROPDOWN AKTIVITAS - GANTI HIJAU */
    .dropdown-list .dropdown-header {
        background-color: #0f5a3f !important;
        /* hijau pertanian */
        color: #ffffff !important;
        font-weight: 600;
        letter-spacing: .5px;
    }

    .text-primary {
        color: var(--agri-primary) !important;
    }

    /* icon circle di dropdown aktivitas */
    .icon-circle.bg-primary {
        background-color: var(--agri-primary) !important;
    }

    /* optional: highlight focus input biar nyambung tema */
    .form-control:focus {
        border-color: var(--agri-secondary) !important;
        box-shadow: 0 0 0 .2rem rgba(20, 122, 82, .25) !important;
    }
</style>

@php
    use App\Models\Activity;

    $latestActivities = collect();
    $badgeCount = 0;

    // Avatar
    $user = auth()->user();
    $avatarUrl = asset('img/undraw_profile.svg');
    if ($user && !empty($user->avatar)) {
        $avatarUrl = asset('storage/' . $user->avatar);
    }

    if (auth()->check()) {
        $latestActivities = Activity::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        // badge merah (anggap terbaru = 24 jam terakhir)
        $badgeCount = Activity::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDay())
            ->count();
    }

    $badgeText = $badgeCount > 9 ? '9+' : (string) $badgeCount;
@endphp

{{-- ✅ UCAPAN SELAMAT DATANG (SATU SAJA) --}}
@if (session('login_success'))
    <div id="welcomeToast" class="alert alert-success alert-dismissible fade show mb-3 shadow" role="alert"
        style="position: fixed; top: 90px; right: 20px; z-index: 1050; min-width: 280px;">
        <strong>Selamat datang!</strong> {{ auth()->user()->name ?? 'User' }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <script>
        // auto close 3 detik
        setTimeout(function() {
            var el = document.getElementById('welcomeToast');
            if (el && window.$) {
                $(el).alert('close');
            } else if (el) {
                el.remove();
            }
        }, 3000);
    </script>
@endif

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search (DISABLE TAMPILAN, KODE TETAP ADA) -->
    <form class="d-none form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search"
        action="{{ route('search.index') }}" method="GET">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small"
                placeholder="Cari SPT / Petugas / Poktan..." name="q" value="{{ request('q') }}"
                aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">

                {{-- FORM MOBILE (DISABLE TAMPILAN, KODE TETAP ADA) --}}
                <form class="d-none form-inline mr-auto w-100 navbar-search" action="{{ route('search.index') }}"
                    method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Cari..."
                            name="q" value="{{ request('q') }}" aria-label="Search"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </li>
        <!-- 🔍 SEARCH ICON (SEMUA UKURAN LAYAR) -->
        <li class="nav-item mx-1">
            <a class="nav-link" href="{{ route('search.index') }}" title="Pencarian">
                <i class="fas fa-search fa-fw"></i>
            </a>
        </li>


        <!-- ✅ Nav Item - Alerts (Aktivitas) -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>

                @if ($badgeCount > 0)
                    <span class="badge badge-danger badge-counter">{{ $badgeText }}</span>
                @endif
            </a>

            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">



                {{-- HEADER + ICON HAPUS DI POJOK KANAN --}}
                <div class="d-flex align-items-center justify-content-between dropdown-header">
                    <span>AKTIVITAS TERAKHIR</span>

                    <form method="POST" action="{{ route('activities.destroyAll') }}"
                        onsubmit="return confirm('Hapus semua aktivitas?')" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-white p-0" title="Hapus Aktivitas"
                            style="text-decoration:none;">
                            <i class="fas fa-trash-alt mr-1"></i>
                            <span class="small">Hapus Aktivitas</span>
                        </button>
                    </form>
                </div>

                @forelse($latestActivities as $act)
                    @php
                        $payload = $act->payload;
                        if (is_string($payload)) {
                            $decoded = json_decode($payload, true);
                            $payload = is_array($decoded) ? $decoded : [];
                        } elseif (!is_array($payload)) {
                            $payload = [];
                        }

                        $ket = $payload['keterangan'] ?? ucfirst((string) $act->action);
                        $redirect = $payload['redirect_url'] ?? route('spt.index');

                        $data = $payload['data'] ?? [];
                        if (is_string($data)) {
                            $decodedData = json_decode($data, true);
                            $data = is_array($decodedData) ? $decodedData : [];
                        } elseif (!is_array($data)) {
                            $data = [];
                        }

                        $miniParts = [];
                        if (!empty($data['nomor_surat'])) {
                            $miniParts[] = $data['nomor_surat'];
                        }
                        if (!empty($data['nomor_kwitansi'])) {
                            $miniParts[] = $data['nomor_kwitansi'];
                        }
                        $mini = implode(' | ', $miniParts);

                        if (empty($redirect)) {
                            $redirect = '#';
                        }
                    @endphp

                    <a class="dropdown-item d-flex align-items-center" href="{{ $redirect }}">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">
                                {{ optional($act->created_at)->diffForHumans() }}
                            </div>
                            <span class="font-weight-bold">{{ $ket }}</span>
                            @if ($mini !== '')
                                <div class="small text-gray-500">{{ $mini }}</div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="dropdown-item text-center small text-gray-500">
                        Belum ada aktivitas.
                    </div>
                @endforelse
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ auth()->user()->name ?? 'User' }}
                </span>
                <img class="img-profile rounded-circle" src="{{ $avatarUrl }}" alt="avatar">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>
</nav>

<!-- ✅ MODAL LOGOUT (WAJIB ADA) -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Yakin ingin logout?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">
                Klik "Logout" untuk keluar dari aplikasi.
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>

                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
