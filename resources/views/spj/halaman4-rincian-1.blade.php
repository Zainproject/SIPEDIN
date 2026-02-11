@php
    use Carbon\Carbon;

    // penerima default
    $penerima = $penerima ?? $spt->petugasList()->first();

    // tanggal
    $tglLampiran = now()->translatedFormat('d F Y');

    // lama hari
    $start = $spt->tanggal_berangkat ? Carbon::parse($spt->tanggal_berangkat) : null;
    $end = $spt->tanggal_kembali ? Carbon::parse($spt->tanggal_kembali) : null;
    $lama = $start && $end ? $start->diffInDays($end) + 1 : 0;

    // ambil item biaya
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
            'harga' => $h, // per hari
        ];

        $subtotalHitung += $h;
    }

    $subtotalPerhari = !is_null($spt->subtotal_perhari) ? (float) $spt->subtotal_perhari : (float) $subtotalHitung;

    if (count($items) === 0) {
        $items[] = [
            'keterangan' => 'Biaya Bantuan Transport',
            'harga' => $subtotalPerhari,
        ];
    }

    $total = (float) ($spt->total_biaya ?? $subtotalPerhari * $lama);

    // user login
    $user = $user ?? auth()->user();

    // pejabat (TTD kanan bawah)
    $namaPejabat = $user->nama ?? ($user->name ?? '-');
    $nipPejabat = $user->nip ?? '-';
    $jabatanPejabat = $user->jabatan ?? 'Pejabat Pembuat Komitmen';

    // bendahara (TTD kiri bawah)
    $namaBendahara = $user->bendahara_nama ?? '-';
    $nipBendahara = $user->bendahara_nip ?? '-';
    $jabatanBendahara = $user->bendahara_jabatan ?? 'Bendahara Pengeluaran';

    // penerima
    $namaPenerima = $penerima->nama ?? '-';
    $nipPenerima = $penerima->nip ?? '-';
@endphp

<div class="page">

    {{-- JUDUL --}}
    <div style="text-align:center; font-weight:bold; text-transform:uppercase;">
        <span style="text-decoration: underline;">RINCIAN BIAYA PERJALANAN DINAS (REALISASI)</span>
    </div>

    <br>

    {{-- INFO --}}
    <table style="width:65%; margin-left:10px; font-size:10.5pt;">
        <tr>
            <td style="width:38%;">Lampiran SPD Nomor</td>
            <td style="width:3%;">:</td>
            <td>{{ $spt->nomor_surat }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $tglLampiran }}</td>
        </tr>
    </table>

    <br>

    {{-- TABEL RINCIAN --}}
    <table border="1" cellpadding="6" cellspacing="0"
        style="width:92%; margin:0 auto; border-collapse:collapse; font-size:10.5pt;">
        <tr style="background:#d9d9d9; text-align:center; font-weight:bold;">
            <td style="width:6%;">NO</td>
            <td style="width:54%;">PERINCIAN BIAYA</td>
            <td style="width:20%;">JUMLAH (Rp)</td>
            <td style="width:20%;">KETERANGAN</td>
        </tr>

        @php $no=1; @endphp
        @foreach ($items as $it)
            <tr>
                <td style="text-align:center;">{{ $no++ }}.</td>
                <td>{{ $it['keterangan'] }}</td>
                <td style="text-align:right;">{{ number_format($it['harga'], 0, ',', '.') }},</td>
                <td style="text-align:center;">{{ $lama ? $lama . ' hari' : '' }}</td>
            </tr>
        @endforeach

        {{-- baris kosong biar mirip template --}}
        @for ($i = count($items); $i < 5; $i++)
            <tr style="height:22px;">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor

        <tr style="font-weight:bold;">
            <td></td>
            <td>JUMLAH</td>
            <td style="text-align:right;">{{ number_format($total, 0, ',', '.') }},</td>
            <td></td>
        </tr>
    </table>

    <br><br>

    {{-- BLOK TTD 2 KOLOM (BENDHARA + PENERIMA) --}}
    <table style="width:92%; margin:0 auto; font-size:10.5pt;">
        <tr>
            <td style="width:50%; vertical-align:top;">
                Telah dibayar sejumlah<br>
                <b>Rp. {{ number_format($total, 0, ',', '.') }},-</b><br><br>
                {{ $jabatanBendahara }}<br><br><br><br>

                <b style="text-transform:uppercase; text-decoration: underline;">
                    {{ strtoupper($namaBendahara) }}
                </b><br>
                <span style="white-space: pre;">NIP. {{ $nipBendahara }}</span>
            </td>

            <td style="width:50%; vertical-align:top; text-align:left; padding-left:40px;">
                Sumenep, {{ $tglLampiran }}<br>
                Telah menerima jumlah sebesar<br>
                <b>Rp. {{ number_format($total, 0, ',', '.') }},-</b><br><br>
                Yang Menerima,<br><br><br><br>

                <b style="text-transform:uppercase; text-decoration: underline;">
                    {{ strtoupper($namaPenerima) }}
                </b><br>
                <span style="white-space: pre;">NIP. {{ $nipPenerima }}</span>
            </td>
        </tr>
    </table>

    <br><br>

    {{-- GARIS PEMBATAS --}}
    <div style="width:92%; margin:0 auto; border-top:1px solid #000;"></div>

    <br>

    {{-- JUDUL PERHITUNGAN --}}
    <div style="text-align:center; font-weight:bold; text-transform:uppercase; font-size:10.5pt;">
        PERHITUNGAN SPPD RAMPUNG
    </div>

    <br>

    {{-- PERHITUNGAN (KIRI) + TTD PEJABAT (KANAN) --}}
    <table>
        <tr>
            {{-- kiri perhitungan --}}
            <td style="width:60%; vertical-align:top;">
                <table style="width:100%;">
                    <tr>
                        <td style="width:55%;">Ditetapkan Sejumlah</td>
                        <td style="width:5%;">:</td>
                        <td style="width:10%;">Rp.</td>
                        <td style="text-align:right;"><b>{{ number_format($total, 0, ',', '.') }},-</b></td>
                    </tr>
                    <tr>
                        <td>Yang Telah Dibayar Semula</td>
                        <td>:</td>
                        <td>Rp.</td>
                        <td style="text-align:right;"><b>{{ number_format($total, 0, ',', '.') }},-</b></td>
                    </tr>
                    <tr>
                        <td>Sisa Telah Dibayar Semula</td>
                        <td>:</td>
                        <td>Rp.</td>
                        <td style="text-align:right;">-</td>
                    </tr>
                    <tr>
                        <td>Sisa Kurang / Lebih</td>
                        <td>:</td>
                        <td>Rp.</td>
                        <td style="text-align:right;">-</td>
                    </tr>
                </table>
            </td>

            {{-- kanan TTD pejabat --}}
            <td style="width:40%; vertical-align:top; text-align:left; padding-top:105px;">
                An. Kuasa Pengguna Anggaran<br>
                Pejabat Pembuat Komitmen<br><br><br><br>

                <b style="text-transform:uppercase; text-decoration: underline;">
                    {{ strtoupper($namaPejabat) }}
                </b><br>
                <span style="white-space: pre;">NIP. {{ $nipPejabat }}</span>
            </td>

        </tr>
    </table>

</div>
