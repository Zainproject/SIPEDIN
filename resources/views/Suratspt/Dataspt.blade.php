@extends('index')

@section('main')
    @php
        // helper romawi untuk tampilan jika dibutuhkan
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        // helper decode aman
        $toArr = function ($v) {
            if (is_array($v)) {
                return array_values($v);
            }
            if (is_null($v)) {
                return [];
            }
            if (is_string($v)) {
                $v = trim($v);
                if ($v === '') {
                    return [];
                }
                $decoded = json_decode($v, true);
                return is_array($decoded) ? array_values($decoded) : [$v];
            }
            return array_values((array) $v);
        };
    @endphp

    <div id="content">
        <div class="container-fluid">

            <div class="d-sm-flex align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Data SPT</h1>
                    <small class="text-muted">Kelola data SPT: tambah, detail, edit, hapus, dan cetak.</small>
                </div>

                <div class="d-flex" style="gap:8px;">
                    <a href="{{ route('spt.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah SPT
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-body">

                    {{-- Toolbar --}}
                    <div class="row mb-3">
                        <div class="col-md-5 mb-2">
                            <input type="text" id="searchBox" class="form-control"
                                placeholder="Cari: nomor surat, kwitansi, keperluan, berangkat dari, MAK...">
                        </div>

                        <div class="col-md-3 mb-2">
                            <select id="filterBulan" class="form-control">
                                <option value="">-- Filter Bulan --</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">
                                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-2 mb-2">
                            <select id="filterTahun" class="form-control">
                                <option value="">-- Tahun --</option>
                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-2 mb-2 d-flex" style="gap:8px;">
                            <button type="button" id="btnReset" class="btn btn-secondary btn-block">
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tableSPT" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th>Nomor</th>
                                    <th>Keperluan</th>
                                    <th>Petugas</th>
                                    <th>Tanggal</th>
                                    <th>Biaya</th>
                                    <th style="width:170px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($spts as $idx => $spt)
                                    @php
                                        $petugasArr = $toArr($spt->petugas ?? []);
                                        $tujuanArr = $toArr($spt->tujuan ?? []);

                                        $poktanNama = $toArr($spt->poktan_nama ?? []);
                                        $desKota = $toArr($spt->deskripsi_kota ?? []);
                                        $desLainnya = $toArr($spt->deskripsi_lainnya ?? []);

                                        $ketBiaya = $toArr($spt->keterangan_biaya ?? []);
                                        $hargaBiaya = $toArr($spt->harga_biaya ?? []);

                                        $arahan = $toArr($spt->arahan ?? []);
                                        $masalah = $toArr($spt->masalah ?? []);
                                        $saran = $toArr($spt->saran ?? []);
                                        $lainnya = $toArr($spt->lainnya ?? []);

                                        $tgl = '-';
                                        if ($spt->tanggal_berangkat && $spt->tanggal_kembali) {
                                            $tgl =
                                                \Carbon\Carbon::parse($spt->tanggal_berangkat)->format('d/m/Y') .
                                                ' - ' .
                                                \Carbon\Carbon::parse($spt->tanggal_kembali)->format('d/m/Y');
                                        }

                                        $bulanVal = (int) ($spt->bulan ?? 0);
                                        $tahunVal = (int) ($spt->tahun ?? 0);

                                        // petugas tampil singkat
                                        $petugasNama = collect($petugasArr)
                                            ->map(function ($nip) use ($petugasMap) {
                                                $nip = trim((string) $nip);
                                                return $petugasMap[$nip] ?? $nip;
                                            })
                                            ->filter()
                                            ->values();

                                        $petugasSingkat = $petugasNama->take(2)->implode(', ');
                                        if ($petugasNama->count() > 2) {
                                            $petugasSingkat .= ' (+' . $petugasNama->count() - 2 . ' org)';
                                        }

                                        // subtotal/total (sudah ada di kolom)
                                        $totalBiaya = $spt->total_biaya ?? null;

                                        // untuk filter + search via data-attribute
                                        $searchBlob = strtolower(
                                            implode(' ', [
                                                $spt->nomor_surat,
                                                $spt->nomor_kwitansi,
                                                $spt->keperluan,
                                                $spt->berangkat_dari,
                                                $spt->mak,
                                                $tgl,
                                                $petugasNama->implode(' '),
                                            ]),
                                        );
                                    @endphp

                                    <tr class="row-spt" data-search="{{ $searchBlob }}" data-bulan="{{ $bulanVal }}"
                                        data-tahun="{{ $tahunVal }}">
                                        <td>{{ $idx + 1 }}</td>

                                        <td>
                                            <div class="font-weight-bold">{{ $spt->nomor_surat ?? '-' }}</div>
                                            <small class="text-muted d-block">Kwitansi:
                                                {{ $spt->nomor_kwitansi ?? '-' }}</small>
                                            <span class="badge badge-info">MAK: {{ $spt->mak ?? '-' }}</span>
                                        </td>

                                        <td>
                                            <div class="font-weight-bold">{{ $spt->keperluan ?? '-' }}</div>
                                            <small class="text-muted">Berangkat dari:
                                                {{ $spt->berangkat_dari ?? '-' }}</small>
                                        </td>

                                        <td>
                                            <span class="badge badge-light">
                                                {{ $petugasSingkat ?: '-' }}
                                            </span>
                                        </td>

                                        <td>
                                            <div>{{ $tgl }}</div>
                                            <small class="text-muted">Lama: {{ $spt->lama_hari ?? '-' }} hari</small>
                                        </td>

                                        <td>
                                            @if ($totalBiaya !== null)
                                                <div class="font-weight-bold">Rp
                                                    {{ number_format((float) $totalBiaya, 0, ',', '.') }}</div>
                                                <small class="text-muted">Subtotal/hari: Rp
                                                    {{ number_format((float) ($spt->subtotal_perhari ?? 0), 0, ',', '.') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                {{-- Detail Modal --}}
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                    data-target="#detailModal{{ $spt->id }}" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                {{-- Edit --}}
                                                <a href="{{ route('spt.edit', $spt->id) }}" class="btn btn-sm btn-warning"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- Print single --}}
                                                <a href="{{ route('spt.print', $spt->id) }}" class="btn btn-sm btn-success"
                                                    title="Print">
                                                    <i class="fas fa-print"></i>
                                                </a>


                                                {{-- Hapus modal --}}
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                                    data-target="#hapusModal{{ $spt->id }}" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            {{-- Modal Hapus --}}
                                            <div class="modal fade" id="hapusModal{{ $spt->id }}" tabindex="-1"
                                                role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <p class="mb-2">Yakin ingin menghapus data SPT ini?</p>
                                                            <div class="alert alert-warning mb-0">
                                                                <div><strong>Nomor:</strong> {{ $spt->nomor_surat ?? '-' }}
                                                                </div>
                                                                <div><strong>Keperluan:</strong>
                                                                    {{ $spt->keperluan ?? '-' }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Batal</button>
                                                            <form action="{{ route('spt.destroy', $spt->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="fas fa-trash"></i> Ya, Hapus
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal Detail --}}
                                            <div class="modal fade" id="detailModal{{ $spt->id }}" tabindex="-1"
                                                role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-scrollable"
                                                    role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Detail SPT</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">

                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="text-muted small">Nomor Surat</div>
                                                                    <div class="font-weight-bold">
                                                                        {{ $spt->nomor_surat ?? '-' }}</div>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="text-muted small">Nomor Kwitansi</div>
                                                                    <div class="font-weight-bold">
                                                                        {{ $spt->nomor_kwitansi ?? '-' }}</div>
                                                                </div>

                                                                <div class="col-md-4 mb-3">
                                                                    <div class="text-muted small">MAK</div>
                                                                    <span
                                                                        class="badge badge-info">{{ $spt->mak ?? '-' }}</span>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <div class="text-muted small">Alat Angkut</div>
                                                                    <div class="font-weight-bold">
                                                                        {{ $spt->alat_angkut ?? '-' }}</div>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <div class="text-muted small">Berangkat Dari</div>
                                                                    <div class="font-weight-bold">
                                                                        {{ $spt->berangkat_dari ?? '-' }}</div>
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <div class="text-muted small">Keperluan</div>
                                                                    <div class="font-weight-bold">
                                                                        {{ $spt->keperluan ?? '-' }}</div>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="text-muted small">Yang Hadir</div>
                                                                    <div class="font-weight-bold">
                                                                        {{ $spt->kehadiran ?? '-' }}</div>
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <div class="text-muted small">Tanggal Berangkat</div>
                                                                    <div class="font-weight-bold">
                                                                        {{ $spt->tanggal_berangkat ? \Carbon\Carbon::parse($spt->tanggal_berangkat)->format('d/m/Y') : '-' }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="text-muted small">Tanggal Kembali</div>
                                                                    <div class="font-weight-bold">
                                                                        {{ $spt->tanggal_kembali ? \Carbon\Carbon::parse($spt->tanggal_kembali)->format('d/m/Y') : '-' }}
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <hr>

                                                            {{-- Petugas --}}
                                                            <h6 class="font-weight-bold">Petugas</h6>
                                                            @if (!empty($petugasArr))
                                                                <ul class="mb-3">
                                                                    @foreach ($petugasArr as $nip)
                                                                        @php $nip = trim((string)$nip); @endphp
                                                                        <li>{{ $petugasMap[$nip] ?? $nip }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <div class="text-muted mb-3">-</div>
                                                            @endif

                                                            {{-- Tujuan --}}
                                                            <h6 class="font-weight-bold">Tujuan & Detail</h6>
                                                            @if (!empty($tujuanArr))
                                                                <div class="table-responsive mb-3">
                                                                    <table class="table table-sm table-bordered">
                                                                        <thead class="thead-light">
                                                                            <tr>
                                                                                <th style="width:50px;">#</th>
                                                                                <th style="width:200px;">Jenis Tujuan</th>
                                                                                <th>Detail</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($tujuanArr as $i2 => $tj)
                                                                                @php
                                                                                    $detail = '-';
                                                                                    if ($tj === 'kelompok_tani') {
                                                                                        $detail =
                                                                                            $poktanNama[$i2] ?? '-';
                                                                                    }
                                                                                    if ($tj === 'kabupaten_kota') {
                                                                                        $detail = $desKota[$i2] ?? '-';
                                                                                    }
                                                                                    if ($tj === 'lainnya') {
                                                                                        $detail =
                                                                                            $desLainnya[$i2] ?? '-';
                                                                                    }
                                                                                @endphp
                                                                                <tr>
                                                                                    <td>{{ $i2 + 1 }}</td>
                                                                                    <td>{{ ucwords(str_replace('_', ' ', $tj)) }}
                                                                                    </td>
                                                                                    <td>{{ $detail }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="text-muted mb-3">-</div>
                                                            @endif

                                                            {{-- Biaya --}}
                                                            <h6 class="font-weight-bold">Rincian Biaya (per hari)</h6>
                                                            @if (!empty($ketBiaya))
                                                                <div class="table-responsive mb-2">
                                                                    <table class="table table-sm table-bordered">
                                                                        <thead class="thead-light">
                                                                            <tr>
                                                                                <th style="width:50px;">#</th>
                                                                                <th>Keterangan</th>
                                                                                <th style="width:200px;"
                                                                                    class="text-right">Harga / Hari</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($ketBiaya as $i3 => $ket)
                                                                                @php $harga = (float)($hargaBiaya[$i3] ?? 0); @endphp
                                                                                <tr>
                                                                                    <td>{{ $i3 + 1 }}</td>
                                                                                    <td>{{ $ket }}</td>
                                                                                    <td class="text-right">Rp
                                                                                        {{ number_format($harga, 0, ',', '.') }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                <div class="row mb-3">
                                                                    <div class="col-md-4">
                                                                        <div class="text-muted small">Lama Hari</div>
                                                                        <div class="font-weight-bold">
                                                                            {{ $spt->lama_hari ?? '-' }} hari</div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="text-muted small">Subtotal / Hari</div>
                                                                        <div class="font-weight-bold">Rp
                                                                            {{ number_format((float) ($spt->subtotal_perhari ?? 0), 0, ',', '.') }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="text-muted small">Total Biaya</div>
                                                                        <div class="font-weight-bold">Rp
                                                                            {{ number_format((float) ($spt->total_biaya ?? 0), 0, ',', '.') }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="text-muted mb-3">-</div>
                                                            @endif

                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <h6 class="font-weight-bold">Arahan</h6>
                                                                    @if (!empty($arahan))
                                                                        <ol class="mb-0">
                                                                            @foreach ($arahan as $x)
                                                                                <li>{{ $x }}</li>
                                                                            @endforeach
                                                                        </ol>
                                                                    @else
                                                                        <div class="text-muted">-</div>
                                                                    @endif
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <h6 class="font-weight-bold">Masalah / Temuan</h6>
                                                                    @if (!empty($masalah))
                                                                        <ol class="mb-0">
                                                                            @foreach ($masalah as $x)
                                                                                <li>{{ $x }}</li>
                                                                            @endforeach
                                                                        </ol>
                                                                    @else
                                                                        <div class="text-muted">-</div>
                                                                    @endif
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <h6 class="font-weight-bold">Saran Tindakan</h6>
                                                                    @if (!empty($saran))
                                                                        <ol class="mb-0">
                                                                            @foreach ($saran as $x)
                                                                                <li>{{ $x }}</li>
                                                                            @endforeach
                                                                        </ol>
                                                                    @else
                                                                        <div class="text-muted">-</div>
                                                                    @endif
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <h6 class="font-weight-bold">Lain-lain</h6>
                                                                    @if (!empty($lainnya))
                                                                        <ol class="mb-0">
                                                                            @foreach ($lainnya as $x)
                                                                                <li>{{ $x }}</li>
                                                                            @endforeach
                                                                        </ol>
                                                                    @else
                                                                        <div class="text-muted">-</div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <hr>
                                                            <div class="d-flex justify-content-between">
                                                                <small class="text-muted">Created:
                                                                    {{ $spt->created_at ? $spt->created_at->format('d/m/Y H:i') : '-' }}</small>
                                                                <small class="text-muted">Updated:
                                                                    {{ $spt->updated_at ? $spt->updated_at->format('d/m/Y H:i') : '-' }}</small>
                                                            </div>

                                                        </div>

                                                        <div class="modal-footer">
                                                            <a href="{{ route('spt.edit', $spt->id) }}"
                                                                class="btn btn-warning">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                            <a href="{{ route('spt.print', $spt->id) }}"
                                                                class="btn btn-sm btn-success" title="Print">
                                                                <i class="fas fa-print"></i>
                                                            </a>

                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Tutup</button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Data belum ada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        <small class="text-muted" id="infoCount"></small>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        (function() {
            const searchBox = document.getElementById('searchBox');
            const filterBulan = document.getElementById('filterBulan');
            const filterTahun = document.getElementById('filterTahun');
            const btnReset = document.getElementById('btnReset');
            const rows = Array.from(document.querySelectorAll('.row-spt'));
            const infoCount = document.getElementById('infoCount');

            function applyFilter() {
                const q = (searchBox.value || '').toLowerCase().trim();
                const bulan = (filterBulan.value || '').trim();
                const tahun = (filterTahun.value || '').trim();

                let shown = 0;

                rows.forEach(row => {
                    const blob = (row.dataset.search || '');
                    const rowBulan = (row.dataset.bulan || '').trim();
                    const rowTahun = (row.dataset.tahun || '').trim();

                    const okQ = !q || blob.includes(q);
                    const okB = !bulan || rowBulan === bulan;
                    const okT = !tahun || rowTahun === tahun;

                    const visible = okQ && okB && okT;
                    row.style.display = visible ? '' : 'none';
                    if (visible) shown++;
                });

                infoCount.textContent = `Menampilkan ${shown} dari ${rows.length} data`;
            }

            searchBox.addEventListener('input', applyFilter);
            filterBulan.addEventListener('change', applyFilter);
            filterTahun.addEventListener('change', applyFilter);

            btnReset.addEventListener('click', function() {
                searchBox.value = '';
                filterBulan.value = '';
                filterTahun.value = '';
                applyFilter();
            });

            applyFilter();
        })();
    </script>
@endsection
