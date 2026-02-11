@php
    use Carbon\Carbon;

    $tglBerangkat = Carbon::parse($spt->tanggal_berangkat)->translatedFormat('d F Y');
    $tglKembali = Carbon::parse($spt->tanggal_kembali)->translatedFormat('d F Y');
    $lama = Carbon::parse($spt->tanggal_berangkat)->diffInDays(Carbon::parse($spt->tanggal_kembali)) + 1;

    $petugasList = $spt->petugasList();
    $petugasUtama = $petugasList->first();

    // cek pengikut
    $adaPengikut = $petugasList->count() > 1;
    $no9 = $adaPengikut ? 9 : 8;
    $no10 = $adaPengikut ? 10 : 9;

    // Narasi tujuan
    $listKT = [];
    $listLokasi = [];

    if (is_array($spt->tujuan)) {
        foreach ($spt->tujuan as $i => $tj) {
            if ($tj === 'kelompok_tani') {
                $listKT[] = 'KT. ' . ($spt->poktan_nama[$i] ?? '-');
            } elseif ($tj === 'kabupaten_kota') {
                $listLokasi[] = $spt->deskripsi_kota[$i] ?? '-';
            } elseif ($tj === 'lainnya') {
                $listLokasi[] = $spt->deskripsi_lainnya[$i] ?? '-';
            }
        }
    }

    if (count($listLokasi) > 0) {
        $lokasiUtama = implode(' dan ', $listLokasi);
    } elseif (count($listKT) > 0) {
        $lokasiUtama = 'Kabupaten Sumenep';
    } else {
        $lokasiUtama = '-';
    }

    $objek = count($listKT) > 0 ? ' pada ' . implode(' dan ', $listKT) : '';
@endphp

<div class="page">

    @include('spj.partials.kop')

    <table border="1" cellpadding="6" cellspacing="0" style="width:100%; border-collapse:collapse">

        <!-- Nomor -->
        <tr>
            <td colspan="4">
                <div style="padding-left:450px; text-align:left;">
                    Nomor : {{ $spt->nomor_surat }} <br>
                    Lembar : I / II
                </div>
            </td>
        </tr>

        <!-- Judul -->
        <tr>
            <td colspan="4" style="text-align:center; font-weight:bold;">
                SURAT PERJALANAN DINAS (SPD)
            </td>
        </tr>

        <!-- 1 -->
        <tr>
            <td width="5%">1</td>
            <td width="35%">Pejabat Berwenang Yang Mengeluarkan SPD</td>
            <td colspan="2">
                KUASA PENGGUNA ANGGARAN DINAS KETAHANAN PANGAN DAN PERTANIAN
                KABUPATEN SUMENEP
            </td>
        </tr>

        <!-- 2 -->
        <tr>
            <td>2</td>
            <td>Nama / NIP Pegawai</td>
            <td colspan="2">
                <b>{{ strtoupper($petugasUtama->nama ?? '-') }}</b><br>
                NIP. {{ $petugasUtama->nip ?? '-' }}
            </td>
        </tr>

        <!-- 3 -->
        <tr>
            <td rowspan="2">3</td>
            <td>a. Pangkat dan Golongan</td>
            <td colspan="2">{{ $petugasUtama->pangkat ?? '-' }}</td>
        </tr>
        <tr>
            <td>b. Jabatan</td>
            <td colspan="2">{{ $petugasUtama->jabatan ?? '-' }}</td>
        </tr>

        <!-- 4 -->
        <tr>
            <td>4</td>
            <td>Maksud Perjalanan Dinas</td>
            <td colspan="2">
                Melakukan koordinasi dan survei lahan dalam rangka kegiatan
                {{ $spt->keperluan }}
                di {{ $lokasiUtama }}
                pada tanggal {{ $tglBerangkat }}{{ $objek }}.
            </td>
        </tr>

        <!-- 5 -->
        <tr>
            <td>5</td>
            <td>Alat Angkut</td>
            <td colspan="2">{{ $spt->alat_angkut }}</td>
        </tr>

        <!-- 6 -->
        <tr>
            <td rowspan="2">6</td>
            <td>a. Tempat Berangkat</td>
            <td colspan="2">Sumenep</td>
        </tr>
        <tr>
            <td>b. Tempat Tujuan</td>
            <td colspan="2">
                {{ count($listKT) > 0 ? implode(' dan ', $listKT) : $lokasiUtama }}
            </td>
        </tr>

        <!-- 7 -->
        <tr>
            <td rowspan="3">7</td>
            <td>a. Lamanya Perjalanan</td>
            <td colspan="2">{{ $lama }} hari</td>
        </tr>
        <tr>
            <td>b. Tanggal Berangkat</td>
            <td colspan="2">{{ $tglBerangkat }}</td>
        </tr>
        <tr>
            <td>c. Tanggal Kembali</td>
            <td colspan="2">{{ $tglKembali }}</td>
        </tr>

        <!-- 8 (hanya kalau ada pengikut) -->
        @if ($adaPengikut)
            <tr>
                <td rowspan="{{ $petugasList->count() }}">8</td>
                <td>Pengikut</td>
                <td style="text-align:center;">Pangkat</td>
                <td style="text-align:center;">Jabatan</td>
            </tr>
            @foreach ($petugasList->skip(1) as $pg)
                <tr>
                    <td><b>{{ strtoupper($pg->nama) }}</b><br>NIP. {{ $pg->nip }}</td>
                    <td style="text-align:center;">{{ $pg->pangkat }}</td>
                    <td style="text-align:center;">{{ $pg->jabatan }}</td>
                </tr>
            @endforeach
        @endif

        <!-- Pembebanan -->
        <tr>
            <td rowspan="2">{{ $no9 }}</td>
            <td>Pembebanan Anggaran</td>
        </tr>
        <tr>
            <td>a. SKPD</td>
            <td colspan="2">
                DINAS KETAHANAN PANGAN DAN PERTANIAN
                KABUPATEN SUMENEP
            </td>
        </tr>
        <tr>
            <td></td>
            <td>b. Kode Rekening</td>
            <td colspan="2">{{ $spt->mak }}</td>
        </tr>

        <!-- Keterangan -->
        <tr>
            <td>{{ $no10 }}</td>
            <td>Keterangan lain-lain</td>
            <td colspan="2">-</td>
        </tr>

    </table>

    <!-- TTD -->
    @php
        // Ambil user login (pejabat)
        $pejabat = $user ?? auth()->user();

        $namaPejabat = $pejabat->nama ?? ($pejabat->name ?? '-');
        $nipPejabat = $pejabat->nip ?? '-';
        $jabatanPejabat = $pejabat->jabatan ?? 'Pejabat Pembuat Komitmen';
    @endphp
    <div class="ttd">
        <p>
            Dikeluarkan di : Sumenep<br>
            Tanggal : {{ now()->translatedFormat('d F Y') }}
        </p>

        <p>
            An. Kuasa Pengguna Anggaran<br>
            Pejabat Pembuat Komitmen
        </p>

        <div class="nama">{{ strtoupper($namaPejabat) }}</div>
        <div class="nip" style="white-space: pre;">NIP. {{ $nipPejabat }}</div>
    </div>

</div>
