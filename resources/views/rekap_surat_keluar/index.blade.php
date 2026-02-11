@extends('index')

@section('main')
    <div id="content">
        <div class="container-fluid">

            {{-- HEADER --}}
            <div class="d-sm-flex align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Rekap Surat Keluar</h1>
                    <div class="text-muted small">Filter & rekap nomor surat/kwitansi. Cetak mengikuti filter aktif.</div>
                </div>

                {{-- PRINT DROPDOWN (SATU SAJA) --}}
                <div class="dropdown">
                    <button class="btn btn-success btn-sm shadow-sm dropdown-toggle" type="button" id="ddPrint"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="ddPrint">
                        <a class="dropdown-item"
                            href="{{ route('rekap-surat-keluar.print', array_merge(request()->query(), ['jenis' => 'spt'])) }}"
                            target="_blank">
                            <i class="fas fa-file-alt mr-2 text-success"></i> Cetak Data SPT
                        </a>

                        <a class="dropdown-item"
                            href="{{ route('rekap-surat-keluar.print', array_merge(request()->query(), ['jenis' => 'petugas'])) }}"
                            target="_blank">
                            <i class="fas fa-user-tie mr-2 text-success"></i> Cetak Rekap Petugas
                        </a>

                        <a class="dropdown-item"
                            href="{{ route('rekap-surat-keluar.print', array_merge(request()->query(), ['jenis' => 'poktan'])) }}"
                            target="_blank">
                            <i class="fas fa-users mr-2 text-success"></i> Cetak Rekap Poktan
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item"
                            href="{{ route('rekap-surat-keluar.print', array_merge(request()->query(), ['jenis' => 'all'])) }}"
                            target="_blank">
                            <i class="fas fa-layer-group mr-2 text-success"></i> Cetak Semua (SPT + Rekap)
                        </a>
                    </div>
                </div>
            </div>

            {{-- FILTER CARD --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="font-weight-bold text-primary">
                        <i class="fas fa-filter mr-2"></i>Filter Data
                    </div>
                    <div class="small text-muted">
                        Tips: pilih dropdown langsung update, atau tekan Enter di pencarian.
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('rekap-surat-keluar.index') }}" id="formFilterRekap">
                        <div class="form-row">

                            {{-- Tahun --}}
                            <div class="col-md-2 mb-2">
                                <label class="small mb-1 text-muted">Tahun</label>
                                <select name="tahun" class="form-control form-control-sm js-autosubmit">
                                    <option value="">Semua</option>
                                    @foreach ($tahunOptions as $th)
                                        <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>
                                            {{ $th }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Bulan --}}
                            <div class="col-md-2 mb-2">
                                <label class="small mb-1 text-muted">Bulan</label>
                                <select name="bulan" class="form-control form-control-sm js-autosubmit">
                                    <option value="">Semua</option>
                                    @for ($b = 1; $b <= 12; $b++)
                                        <option value="{{ $b }}"
                                            {{ (string) request('bulan') === (string) $b ? 'selected' : '' }}>
                                            {{ $b }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- MAK --}}
                            <div class="col-md-2 mb-2">
                                <label class="small mb-1 text-muted">MAK</label>
                                <select name="mak" class="form-control form-control-sm js-autosubmit">
                                    <option value="">Semua</option>
                                    @foreach ($makOptions as $mk)
                                        <option value="{{ $mk }}" {{ request('mak') == $mk ? 'selected' : '' }}>
                                            {{ $mk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Petugas --}}
                            <div class="col-md-3 mb-2">
                                <label class="small mb-1 text-muted">Petugas</label>
                                <select name="petugas" class="form-control form-control-sm js-autosubmit">
                                    <option value="">Semua</option>
                                    @foreach ($petugasOptions as $p)
                                        <option value="{{ $p->nip }}"
                                            {{ request('petugas') == $p->nip ? 'selected' : '' }}>
                                            {{ $p->nama }} ({{ $p->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Keyword --}}
                            <div class="col-md-3 mb-2">
                                <label class="small mb-1 text-muted">Pencarian</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Nomor surat / kwitansi / keperluan / berangkat..."
                                        value="{{ request('keyword') }}">
                                </div>
                            </div>

                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <button class="btn btn-primary btn-sm shadow-sm mr-2" type="submit">
                                <i class="fas fa-filter mr-1"></i> Terapkan
                            </button>

                            <a href="{{ route('rekap-surat-keluar.index') }}"
                                class="btn btn-outline-secondary btn-sm shadow-sm">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>

                            @php
                                $hasFilter =
                                    request()->filled('tahun') ||
                                    request()->filled('bulan') ||
                                    request()->filled('mak') ||
                                    request()->filled('petugas') ||
                                    request()->filled('keyword');
                            @endphp

                            @if ($hasFilter)
                                <span class="badge badge-info badge-pill ml-2">Filter aktif</span>
                            @else
                                <span class="badge badge-light badge-pill ml-2">Tanpa filter</span>
                            @endif
                        </div>
                    </form>

                    {{-- KPI --}}
                    <div class="row mt-3">
                        <div class="col-md-6 mb-2">
                            <div class="card border-left-info shadow-sm h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Surat
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($totalSurat) }}
                                            </div>
                                        </div>
                                        <i class="fas fa-envelope-open-text fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="card border-left-success shadow-sm h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total
                                                Biaya</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Rp {{ number_format($totalBiaya ?? 0, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 1) DATA SPT --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-2"></i>Data SPT (Sesuai Filter)
                    </div>
                    <div class="small text-muted">
                        Menampilkan: <b>{{ $spts->count() }}</b> data
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="white-space:nowrap;">No</th>
                                    <th>No Surat</th>
                                    <th>No Kwitansi</th>
                                    <th style="white-space:nowrap;">Tahun/Bulan</th>
                                    <th style="min-width:260px;">Keperluan</th>
                                    <th style="white-space:nowrap;">Berangkat - Kembali</th>
                                    <th class="text-right" style="white-space:nowrap;">Total Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($spts as $spt)
                                    @php
                                        $tgl1 = $spt->tanggal_berangkat
                                            ? \Carbon\Carbon::parse($spt->tanggal_berangkat)->format('d-m-Y')
                                            : '-';
                                        $tgl2 = $spt->tanggal_kembali
                                            ? \Carbon\Carbon::parse($spt->tanggal_kembali)->format('d-m-Y')
                                            : '-';
                                    @endphp
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td><span class="font-weight-bold">{{ $spt->nomor_surat ?? '-' }}</span></td>
                                        <td>{{ $spt->nomor_kwitansi ?? '-' }}</td>
                                        <td><span
                                                class="badge badge-light">{{ $spt->tahun ?? '-' }}/{{ $spt->bulan ?? '-' }}</span>
                                        </td>
                                        <td style="white-space:normal;">{{ $spt->keperluan ?? '-' }}</td>
                                        <td>{{ $tgl1 }} <span class="text-muted">s/d</span> {{ $tgl2 }}
                                        </td>
                                        <td class="text-right">Rp {{ number_format($spt->total_biaya ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 2) REKAP PETUGAS --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="font-weight-bold text-primary">
                        <i class="fas fa-user-tie mr-2"></i>Rekap Petugas (Sesuai Filter)
                    </div>
                    <div class="small text-muted">Ringkasan jumlah SPT per petugas</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th>Petugas</th>
                                    <th class="text-right" style="width:160px; white-space:nowrap;">Jumlah SPT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapPetugas as $i => $rp)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $rp['nama'] }} <span
                                                class="text-muted small">({{ $rp['nip'] }})</span></td>
                                        <td class="text-right font-weight-bold">{{ number_format($rp['jumlah']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Tidak ada data rekap petugas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 3) REKAP POKTAN --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="font-weight-bold text-primary">
                        <i class="fas fa-users mr-2"></i>Rekap Poktan (Sesuai Filter)
                    </div>
                    <div class="small text-muted">Ringkasan jumlah SPT per poktan</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th>Poktan</th>
                                    <th class="text-right" style="width:160px; white-space:nowrap;">Jumlah SPT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rekapPoktan = $rekapPoktan ?? []; @endphp
                                @forelse($rekapPoktan as $i => $rk)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $rk['nama_poktan'] ?? ($rk['nama'] ?? '-') }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($rk['jumlah'] ?? 0) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Tidak ada data rekap poktan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.js-autosubmit').forEach(function(el) {
                el.addEventListener('change', function() {
                    document.getElementById('formFilterRekap').submit();
                });
            });
        </script>
    @endpush
@endsection
