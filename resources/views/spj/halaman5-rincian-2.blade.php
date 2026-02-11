@php
    use Carbon\Carbon;

    /*
    |--------------------------------------------------------------------------
    | PENERIMA (WAJIB dari cetak.blade.php saat loop)
    |--------------------------------------------------------------------------
    */
    $penerima = $penerima ?? $spt->petugasList()->first();

    /*
    |--------------------------------------------------------------------------
    | WAKTU
    |--------------------------------------------------------------------------
    */
    $start = $spt->tanggal_berangkat ? Carbon::parse($spt->tanggal_berangkat) : null;
    $end = $spt->tanggal_kembali ? Carbon::parse($spt->tanggal_kembali) : null;

    $tglBerangkat = $start ? $start->translatedFormat('d F Y') : '-';
    $tglKembali = $end ? $end->translatedFormat('d F Y') : '-';

    // Lama hari (inclusive)
    $lama = $start && $end ? $start->diffInDays($end) + 1 : 0;

    /*
    |--------------------------------------------------------------------------
    | BIAYA ITEM (ARRAY)
    |--------------------------------------------------------------------------
    */
    $ket = is_array($spt->keterangan_biaya) ? array_values($spt->keterangan_biaya) : [];
    $hrg = is_array($spt->harga_biaya) ? array_values($spt->harga_biaya) : [];

    $items = [];
    $subtotalHitung = 0;

    $max = max(count($ket), count($hrg));
    for ($i = 0; $i < $max; $i++) {
        $k = trim((string) ($ket[$i] ?? ''));
        $h = (float) preg_replace('/[^0-9]/', '', (string) ($hrg[$i] ?? 0));

        if ($k === '' && $h == 0) {
            continue;
        }

        $items[] = [
            'keterangan' => $k !== '' ? $k : 'Biaya Bantuan Transport',
            'harga' => $h, // harga per hari
        ];

        $subtotalHitung += $h;
    }

    // subtotal per hari: prioritas kolom subtotal_perhari, fallback hitung dari array
    $subtotalPerhari = !is_null($spt->subtotal_perhari) ? (float) $spt->subtotal_perhari : (float) $subtotalHitung;

    // fallback kalau item kosong (biar tidak blank)
    if (count($items) === 0) {
        $items[] = [
            'keterangan' => 'Biaya Bantuan Transport',
            'harga' => $subtotalPerhari,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */
    $total = (float) ($spt->total_biaya ?? $subtotalPerhari * $lama);
@endphp

<div class="page">
    <!-- JUDUL -->
    <div style="text-align:center; margin-bottom:15px;">
        <b><u>RINCIAN RENCANA BIAYA PERJALANAN DINAS (REALISASI)</u></b>
    </div>

    <!-- INFO SPD -->
    <table style="width:60%; margin-bottom:10px;">
        <tr>
            <td style="width:40%;">Lampiran SPD Nomor</td>
            <td style="width:5%;">:</td>
            <td>{{ $spt->nomor_surat }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $tglBerangkat }}</td>
        </tr>
    </table>

    <!-- TABEL RINCIAN -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:15px;" border="1" cellpadding="4">
        <tr style="background:#d9d9d9; text-align:center; font-weight:bold;">
            <td style="width:5%;">NO</td>
            <td style="width:45%;">PERINCIAN BIAYA</td>
            <td style="width:20%;">JUMLAH (Rp)</td>
            <td style="width:30%;">KETERANGAN</td>
        </tr>

        {{-- ITEM BIAYA (harga per hari) --}}
        @php $no = 1; @endphp
        @foreach ($items as $it)
            <tr>
                <td style="text-align:center;">{{ $no++ }}.</td>
                <td>{{ $it['keterangan'] }}</td>
                <td style="text-align:right;">
                    {{ number_format($it['harga'], 0, ',', '.') }},
                </td>
                <td style="text-align:center;">
                    {{ $lama }} hari
                </td>
            </tr>
        @endforeach

        <!-- BARIS KOSONG (biar mirip template) -->
        @for ($i = count($items); $i < 3; $i++)
            <tr style="height:18px;">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor

        <!-- JUMLAH (PALING BAWAH) -->
        <tr style="font-weight:bold;">
            <td colspan="2" style="text-align:center;">JUMLAH</td>
            <td style="text-align:right;">
                {{ number_format($total, 0, ',', '.') }},
            </td>
            <td></td>
        </tr>
    </table>

    <!-- KETERANGAN PEMBAYARAN -->
    @php
        $user = $user ?? auth()->user();

        // Pejabat
        $namaPejabat = $user->nama ?? ($user->name ?? '-');
        $nipPejabat = $user->nip ?? '-';
        $jabatanPejabat = $user->jabatan ?? 'Pejabat Pembuat Komitmen';

        // Bendahara
        $namaBendahara = $user->bendahara_nama ?? '-';
        $nipBendahara = $user->bendahara_nip ?? '-';
        $jabatanBendahara = $user->bendahara_jabatan ?? 'Bendahara';
    @endphp

    <table style="width:100%; margin-bottom:15px;">
        <tr>
            <td style="width:50%; vertical-align:top;"> <br>
                Telah dibayar sejumlah<br>
                <b>Rp. {{ number_format($total, 0, ',', '.') }},-</b><br><br>
                Bendahara Pengeluaran<br>
                <div class="nama">{{ strtoupper($namaBendahara) }}</div>
                <div class="nip" style="white-space: pre;">NIP. {{ $nipBendahara }}</div>
            </td>
            <td style="width:50%; vertical-align:top;">
                Sumenep, {{ now()->translatedFormat('d F Y') }}<br>
                Telah menerima jumlah sebesar<br>
                <b>Rp. {{ number_format($total, 0, ',', '.') }},-</b><br><br>
                Yang Menerima,<br><br><br><br>
                <b><u>{{ strtoupper($penerima->nama ?? '-') }}</u></b><br>
                NIP. {{ $penerima->nip ?? '-' }}
            </td>
        </tr>
    </table>
</div>
