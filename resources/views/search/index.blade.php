@extends('index')

@section('main')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Pencarian Data</h4>
                <small class="text-muted">Cari SPT, Petugas, dan Poktan dalam satu kolom.</small>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        {{-- SEARCH BOX --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                    </div>
                    <input id="searchInput" type="text" class="form-control" placeholder="Ketik untuk mencari..."
                        autocomplete="off">
                    <div class="input-group-append">
                        <button id="btnClear" class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div id="loading" class="mt-2 d-none text-primary">
                    <span class="spinner-border spinner-border-sm"></span> Memuat...
                </div>
            </div>
        </div>

        {{-- HASIL --}}
        <div id="resultsWrap" class="d-none">

            {{-- SPT --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-file-alt mr-2"></i>Data SPT
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="60">No</th>
                                    <th>Nomor Surat</th>
                                    <th>Nomor Kwitansi</th>
                                    <th>Keperluan</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySpt"></tbody>
                        </table>
                    </div>
                    <div id="emptySpt" class="p-3 text-muted d-none">Tidak ada data SPT</div>
                </div>
            </div>

            {{-- PETUGAS --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-user-tie mr-2"></i>Data Petugas
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="60">No</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyPetugas"></tbody>
                        </table>
                    </div>
                    <div id="emptyPetugas" class="p-3 text-muted d-none">Tidak ada data petugas</div>
                </div>
            </div>

            {{-- POKTAN --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-users mr-2"></i>Data Poktan
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="60">No</th>
                                    <th>Nama Poktan</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyPoktan"></tbody>
                        </table>
                    </div>
                    <div id="emptyPoktan" class="p-3 text-muted d-none">Tidak ada data poktan</div>
                </div>
            </div>

        </div>

    </div>

    @push('scripts')
        <script>
            (function() {

                const input = document.getElementById('searchInput');
                const btnClear = document.getElementById('btnClear');
                const loading = document.getElementById('loading');

                const resultsWrap = document.getElementById('resultsWrap');
                const tbodySpt = document.getElementById('tbodySpt');
                const tbodyPetugas = document.getElementById('tbodyPetugas');
                const tbodyPoktan = document.getElementById('tbodyPoktan');

                const emptySpt = document.getElementById('emptySpt');
                const emptyPetugas = document.getElementById('emptyPetugas');
                const emptyPoktan = document.getElementById('emptyPoktan');

                const setVisible = (el, yes) => el.classList.toggle('d-none', !yes);

                function resetUI() {
                    tbodySpt.innerHTML = '';
                    tbodyPetugas.innerHTML = '';
                    tbodyPoktan.innerHTML = '';

                    setVisible(emptySpt, true);
                    setVisible(emptyPetugas, true);
                    setVisible(emptyPoktan, true);
                    setVisible(resultsWrap, false);
                }

                function bindRowClicks() {
                    document.querySelectorAll('.js-row-link').forEach(tr => {
                        tr.addEventListener('click', function() {
                            const href = this.dataset.href;
                            if (href) window.location.href = href;
                        });
                    });
                }

                function render(payload) {

                    const data = payload?.data ?? {
                        spt: [],
                        petugas: [],
                        poktan: []
                    };
                    setVisible(resultsWrap, true);

                    // SPT → arahkan ke /spt
                    tbodySpt.innerHTML = '';
                    if (data.spt.length) {
                        setVisible(emptySpt, false);
                        data.spt.forEach((row, i) => {
                            tbodySpt.insertAdjacentHTML('beforeend', `
                    <tr class="js-row-link" data-href="{{ route('spt.index') }}" style="cursor:pointer;">
                        <td>${i+1}</td>
                        <td>${row.nomor_surat ?? '-'}</td>
                        <td>${row.nomor_kwitansi ?? '-'}</td>
                        <td>${row.keperluan ?? '-'}</td>
                    </tr>
                `);
                        });
                    } else {
                        setVisible(emptySpt, true);
                    }

                    // PETUGAS
                    tbodyPetugas.innerHTML = '';
                    if (data.petugas.length) {
                        setVisible(emptyPetugas, false);
                        data.petugas.forEach((p, i) => {
                            tbodyPetugas.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>${i+1}</td>
                        <td>${p.nip ?? '-'}</td>
                        <td>${p.nama ?? '-'}</td>
                    </tr>
                `);
                        });
                    } else {
                        setVisible(emptyPetugas, true);
                    }

                    // POKTAN
                    tbodyPoktan.innerHTML = '';
                    if (data.poktan.length) {
                        setVisible(emptyPoktan, false);
                        data.poktan.forEach((pk, i) => {
                            tbodyPoktan.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>${i+1}</td>
                        <td>${pk.nama_poktan ?? '-'}</td>
                        <td>${pk.desa ?? '-'}</td>
                        <td>${pk.kecamatan ?? '-'}</td>
                    </tr>
                `);
                        });
                    } else {
                        setVisible(emptyPoktan, true);
                    }

                    bindRowClicks();
                }

                async function fetchResults(q) {

                    setVisible(loading, true);

                    try {
                        const url = new URL("{{ route('search.results') }}", window.location.origin);
                        url.searchParams.set('q', q);

                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const json = await res.json();

                        render(json);

                    } catch (err) {
                        resetUI();
                    }

                    setVisible(loading, false);
                }

                input.addEventListener('input', function() {
                    const q = this.value.trim();
                    if (q.length < 2) {
                        resetUI();
                        return;
                    }
                    fetchResults(q);
                });

                btnClear.addEventListener('click', function() {
                    input.value = '';
                    resetUI();
                });

            })();
        </script>
    @endpush
@endsection
