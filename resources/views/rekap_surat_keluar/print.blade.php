<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Surat Keluar</title>
    <style>
        @page {
            size: A4;
            margin: 14mm 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .no-print {
            margin-bottom: 12px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .toolbar button {
            padding: 6px 10px;
            border: 1px solid #333;
            background: #f5f5f5;
            cursor: pointer;
            margin-right: 6px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header .title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .header .subtitle {
            font-size: 11px;
            color: #333;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 10px 0 12px 0;
            font-size: 11px;
        }

        .meta .box {
            border: 1px solid #000;
            padding: 8px;
            flex: 1;
        }

        .meta .box b {
            display: inline-block;
            min-width: 80px;
        }

        .kpi {
            margin: 6px 0 12px 0;
            font-size: 11px;
        }

        .kpi .pill {
            display: inline-block;
            border: 1px solid #000;
            padding: 6px 10px;
            margin-right: 8px;
        }

        hr.sep {
            border: none;
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        .section-title {
            font-weight: 700;
            margin: 12px 0 6px 0;
            text-transform: uppercase;
            letter-spacing: .4px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #eee;
            font-weight: 700;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            margin-top: 10px;
            font-size: 10px;
            color: #333;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>

    {{-- Toolbar --}}
    <div class="no-print toolbar">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    @php
        $jenis = $jenis ?? 'all';

        $jenisLabel =
            [
                'spt' => 'DATA SPT',
                'petugas' => 'REKAP PETUGAS',
                'poktan' => 'REKAP POKTAN',
                'all' => 'SEMUA (SPT + REKAP)',
            ][$jenis] ?? 'SEMUA (SPT + REKAP)';

        $filterInfo = $filterInfo ?? [
            'tahun' => null,
            'bulan' => null,
            'mak' => null,
            'petugas' => null,
            'keyword' => null,
        ];

        $rekapPetugas = $rekapPetugas ?? collect();
        $rekapPoktan = $rekapPoktan ?? collect();
    @endphp

    {{-- Header --}}
    <div class="header">
        <div class="title">REKAP SURAT KELUAR</div>
        <div class="subtitle">Mode Cetak: <b>{{ $jenisLabel }}</b></div>
    </div>

    {{-- Filter + KPI --}}
    <div class="meta">
        <div class="box">
            <div><b>Tahun</b>: {{ $filterInfo['tahun'] ?: 'Semua' }}</div>
            <div><b>Bulan</b>: {{ $filterInfo['bulan'] ?: 'Semua' }}</div>
            <div><b>MAK</b>: {{ $filterInfo['mak'] ?: 'Semua' }}</div>
        </div>
        <div class="box">
            <div><b>Petugas</b>: {{ $filterInfo['petugas'] ?: 'Semua' }}</div>
            <div><b>Keyword</b>: {{ $filterInfo['keyword'] ?: '-' }}</div>
            <div><b>Tanggal Cetak</b>: {{ now()->format('d-m-Y H:i') }}</div>
        </div>
    </div>

    <div class="kpi">
        <span class="pill"><b>Total Surat:</b> {{ number_format($totalSurat ?? 0) }}</span>
        <span class="pill"><b>Total Biaya:</b> Rp {{ number_format($totalBiaya ?? 0, 0, ',', '.') }}</span>
    </div>

    <hr class="sep">

    {{-- =======================
        1) CETAK SPT (SAJA / ALL)
       ======================= --}}
    @if ($jenis === 'spt' || $jenis === 'all')
        <div class="section-title">Data SPT</div>
        <table>
            <thead>
                <tr>
                    <th class="nowrap" style="width:40px;">No</th>
                    <th>No Surat</th>
                    <th>No Kwitansi</th>
                    <th class="nowrap" style="width:90px;">Tahun/Bulan</th>
                    <th>Keperluan</th>
                    <th class="nowrap" style="width:120px;">Berangkat - Kembali</th>
                    <th class="nowrap right" style="width:110px;">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($spts as $i => $spt)
                    @php
                        $tgl1 = $spt->tanggal_berangkat
                            ? \Carbon\Carbon::parse($spt->tanggal_berangkat)->format('d-m-Y')
                            : '-';
                        $tgl2 = $spt->tanggal_kembali
                            ? \Carbon\Carbon::parse($spt->tanggal_kembali)->format('d-m-Y')
                            : '-';
                    @endphp
                    <tr>
                        <td class="center nowrap">{{ $i + 1 }}</td>
                        <td>{{ $spt->nomor_surat ?? '-' }}</td>
                        <td>{{ $spt->nomor_kwitansi ?? '-' }}</td>
                        <td class="center nowrap">{{ $spt->tahun ?? '-' }}/{{ $spt->bulan ?? '-' }}</td>
                        <td>{{ $spt->keperluan ?? '-' }}</td>
                        <td class="nowrap">{{ $tgl1 }} s/d {{ $tgl2 }}</td>
                        <td class="right nowrap">Rp {{ number_format($spt->total_biaya ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- Kalau all: kasih pemisah halaman biar rapi --}}
    @if ($jenis === 'all')
        <div class="page-break"></div>
    @endif

    {{-- =======================
        2) CETAK REKAP PETUGAS (SAJA / ALL)
       ======================= --}}
    @if ($jenis === 'petugas' || $jenis === 'all')
        <div class="section-title">Rekap Petugas</div>
        <table>
            <thead>
                <tr>
                    <th class="nowrap" style="width:40px;">No</th>
                    <th>Petugas</th>
                    <th class="nowrap right" style="width:120px;">Jumlah SPT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapPetugas as $i => $rp)
                    <tr>
                        <td class="center nowrap">{{ $i + 1 }}</td>
                        <td>{{ $rp['nama'] ?? '-' }} <span class="nowrap">({{ $rp['nip'] ?? '-' }})</span></td>
                        <td class="right nowrap">{{ number_format($rp['jumlah'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="center">Tidak ada data rekap petugas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if ($jenis === 'all')
        <div class="page-break"></div>
    @endif

    {{-- =======================
        3) CETAK REKAP POKTAN (SAJA / ALL)
       ======================= --}}
    @if ($jenis === 'poktan' || $jenis === 'all')
        <div class="section-title">Rekap Poktan</div>
        <table>
            <thead>
                <tr>
                    <th class="nowrap" style="width:40px;">No</th>
                    <th>Poktan</th>
                    <th class="nowrap right" style="width:120px;">Jumlah SPT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapPoktan as $i => $rk)
                    <tr>
                        <td class="center nowrap">{{ $i + 1 }}</td>
                        <td>{{ $rk['nama_poktan'] ?? ($rk['nama'] ?? '-') }}</td>
                        <td class="right nowrap">{{ number_format($rk['jumlah'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="center">Tidak ada data rekap poktan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        <div>Dicetak oleh: {{ auth()->user()->name ?? 'User' }}</div>
        <div>— Rekap Surat Keluar —</div>
    </div>

</body>

</html>
