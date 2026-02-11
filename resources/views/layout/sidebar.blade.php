<ul class="navbar-nav sidebar sidebar-dark accordion shadow-sm" id="accordionSidebar"
    style="
        background: linear-gradient(180deg, #0b3d2e 0%, #0f5a3f 55%, #147a52 100%);
        border-top-right-radius: 18px;
        border-bottom-right-radius: 18px;
    ">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex flex-column align-items-center justify-content-center" href="{{ url('index') }}"
        style="margin-top: 18px; padding-bottom: 16px;">

        <img src="{{ asset('img/sumenep.png') }}" alt="Logo Instansi" style="height: 34px;">

        <div class="sidebar-brand-text text-white fw-semibold text-center"
            style="font-size: 0.9rem; margin-top: 6px; line-height: 1.3;">
            SIPEDIN
            <div style="font-size: 0.7rem; font-weight: 400; opacity: 0.9;">
                Sistem Informasi Perintah Dinas
            </div>
        </div>
    </a>
    <br>
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->is('index') ? 'active' : '' }}">
        <a class="nav-link text-white {{ request()->is('index') ? 'is-active' : '' }}" href="{{ url('index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Persuratan -->
    <div class="sidebar-heading">
        Persuratan
    </div>

    <li class="nav-item {{ request()->is('spt*') ? 'active' : '' }}">
        <a class="nav-link text-white {{ request()->is('spt*') ? 'is-active' : '' }}" href="{{ url('spt') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Surat Perintah Tugas</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('rekap-surat-keluar*') ? 'active' : '' }}">
        <a class="nav-link text-white {{ request()->is('rekap-surat-keluar*') ? 'is-active' : '' }}"
            href="{{ route('rekap-surat-keluar.index') }}">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>Rekap Surat Keluar</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Master Data -->
    <div class="sidebar-heading">
        Master Data
    </div>

    <!-- Database -->
    <li class="nav-item">
        <a class="nav-link text-white collapsed" href="#" data-toggle="collapse" data-target="#collapseDatabase"
            aria-expanded="true" aria-controls="collapseDatabase">
            <i class="fas fa-fw fa-database"></i>
            <span>Basis Data</span>
        </a>

        <div id="collapseDatabase"
            class="collapse {{ request()->is('petugas*') || request()->is('poktan*') ? 'show' : '' }}"
            data-parent="#accordionSidebar">

            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('petugas*') ? 'active' : '' }}" href="{{ url('petugas') }}">
                    Data Petugas
                </a>
                <a class="collapse-item {{ request()->is('poktan*') ? 'active' : '' }}" href="{{ url('poktan') }}">
                    Data Poktan
                </a>
            </div>
        </div>
    </li>

    <!-- Import -->
    <li class="nav-item {{ request()->is('import*') ? 'active' : '' }}">
        <a class="nav-link text-white {{ request()->is('import*') ? 'is-active' : '' }}"
            href="{{ route('import.index') }}">
            <i class="fas fa-file-import"></i>
            <span>Import Data</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline pb-3">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
