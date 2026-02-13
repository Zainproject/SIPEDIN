@php
    use Carbon\Carbon;

    /*
    |--------------------------------------------------------------------------
    | WAKTU
    |--------------------------------------------------------------------------
    */
    $start = $spt->tanggal_berangkat ? Carbon::parse($spt->tanggal_berangkat) : null;
    $end = $spt->tanggal_kembali ? Carbon::parse($spt->tanggal_kembali) : null;

    $tglBerangkat = $start ? $start->translatedFormat('d F Y') : '-';
    $tglKembali = $end ? $end->translatedFormat('d F Y') : '-';

    $lama = $start && $end ? $start->diffInDays($end) + 1 : 0;

    $waktuRingkas = $start && $end ? $tglBerangkat . ' s/d ' . $tglKembali . ' (' . $lama . ' hari)' : '-';

    /*
    |--------------------------------------------------------------------------
    | BIAYA (ambil dari array jika ada)
    |--------------------------------------------------------------------------
    */
    $hargaArr = is_array($spt->harga_biaya) ? array_values($spt->harga_biaya) : [];
    $subtotalHitung = 0;

    foreach ($hargaArr as $h) {
        $subtotalHitung += (float) preg_replace('/[^0-9]/', '', (string) $h);
    }

    // subtotal per hari: prioritas kolom subtotal_perhari, fallback hasil hitung array
    $biayaPerhari = !is_null($spt->subtotal_perhari) ? (float) $spt->subtotal_perhari : (float) $subtotalHitung;

    // total: prioritas total_biaya, fallback lama * biayaPerhari
    $total = (float) ($spt->total_biaya ?? $lama * $biayaPerhari);

    /*
    |--------------------------------------------------------------------------
    | PENERIMA (WAJIB DARI cetak.blade.php)
    |--------------------------------------------------------------------------
    */
    $penerima = $penerima ?? $spt->petugasList()->first();

    /*
    |--------------------------------------------------------------------------
    | TERBILANG (AMAN UNTUK INCLUDE BERULANG)
    |--------------------------------------------------------------------------
    */
    $terbilang = function ($angka) {
        $fmt = new NumberFormatter('id', NumberFormatter::SPELLOUT);
        return ucfirst($fmt->format((int) round($angka))) . ' Rupiah';
    };

    /*
    |--------------------------------------------------------------------------
    | TUJUAN (DIRINGKAS & DIGABUNG)
    |--------------------------------------------------------------------------
    */
    $norm = fn($v) => trim((string) ($v ?? ''));

    $kt = collect();
    $lokasi = collect();

    $tujuan = is_array($spt->tujuan) ? array_values($spt->tujuan) : [];
    $poktan = is_array($spt->poktan_nama) ? array_values($spt->poktan_nama) : [];
    $kota = is_array($spt->deskripsi_kota) ? array_values($spt->deskripsi_kota) : [];
    $lain = is_array($spt->deskripsi_lainnya) ? array_values($spt->deskripsi_lainnya) : [];

    foreach ($tujuan as $i => $tj) {
        $tj = $norm($tj);
        if ($tj === '') {
            continue;
        }

        if ($tj === 'kelompok_tani') {
            $val = $norm($poktan[$i] ?? '');
            if ($val !== '') {
                $kt->push('KT. ' . $val);
            }
        } elseif ($tj === 'kabupaten_kota') {
            $val = $norm($kota[$i] ?? '');
            if ($val !== '') {
                $lokasi->push($val);
            }
        } elseif ($tj === 'lainnya') {
            $val = $norm($lain[$i] ?? '');
            if ($val !== '') {
                $lokasi->push($val);
            }
        }
    }

    $kt = $kt->unique()->values();
    $lokasi = $lokasi->unique()->values();

    if ($lokasi->count() > 0) {
        $lokasiUtama = $lokasi->implode(' dan ');
    } elseif ($kt->count() > 0) {
        $lokasiUtama = 'Kabupaten Sumenep';
    } else {
        $lokasiUtama = '-';
    }

    $objek = $kt->count() ? ' di ' . $kt->implode(' dan ') : '';
@endphp

<div class="page">

    <!-- TABEL KANAN ATAS -->
    <div style="width:100%; margin-bottom:25px;">
        <table style="width:45%; margin-left:auto; border-collapse:collapse;" border="1" cellpadding="4">
            <tr>
                <td style="width:45%;">&nbsp;Tahun Anggaran</td>
                <td>: {{ $spt->tahun }}</td>
            </tr>
            <tr>
                <td>&nbsp;Nomor Bukti</td>
                <td>: {{ $spt->nomor_kwitansi }}</td>
            </tr>
            <tr>
                <td>&nbsp;MAK</td>
                <td>: {{ $spt->mak }}</td>
            </tr>
            <tr>
                <td>&nbsp;DIPA</td>
                <td>: -</td>
            </tr>
        </table>
    </div>

    <!-- JUDUL -->
    <div style="text-align:center; margin:30px 0;">
        <b><u>KUITANSI / BUKTI PEMBAYARAN</u></b>
    </div>

    <!-- ISI -->
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:22%;">Sudah Terima Dari</td>
            <td style="width:3%;">:</td>
            <td>
                KUASA PENGGUNA ANGGARAN DINAS KETAHANAN PANGAN DAN PERTANIAN
                KABUPATEN SUMENEP
            </td>
        </tr>

        <tr>
            <td>Jumlah Uang</td>
            <td>:</td>
            <td><b>Rp. {{ number_format($total, 0, ',', '.') }},-</b></td>
        </tr>

        <tr>
            <td>Terbilang</td>
            <td>:</td>
            <td><i>{{ $terbilang($total) }}</i></td>
        </tr>

        <tr>
            <td style="vertical-align:top;">Untuk Pembayaran</td>
            <td style="vertical-align:top;">:</td>
            <td style="text-align:justify;">
                @php
                    // ====== Narasi sama persis dengan UNTUK (keperluan dipisah ; lalu digabung dan) ======
                    $norm = function ($val) {
                        if (is_array($val)) {
                            return $val;
                        }

                        if (is_string($val)) {
                            $val = trim($val);
                            if ($val === '') {
                                return [];
                            }

                            $decoded = json_decode($val, true);
                            if (is_array($decoded)) {
                                return $decoded;
                            }

                            $val = str_replace(['|'], ';', $val);
                            return array_values(array_filter(array_map('trim', explode(';', $val))));
                        }

                        return [];
                    };

                    $tujuan = $norm($spt->tujuan);
                    $poktanNama = $norm($spt->poktan_nama);
                    $deskripsiKota = $norm($spt->deskripsi_kota);
                    $deskripsiLainnya = $norm($spt->deskripsi_lainnya);

                    $keperluanParts = array_values(
                        array_filter(array_map('trim', explode(';', (string) $spt->keperluan))),
                    );
                    $getKeperluan = function ($i) use ($keperluanParts, $spt) {
                        if (count($keperluanParts) === 0) {
                            return (string) $spt->keperluan;
                        }
                        return $keperluanParts[$i] ?? $keperluanParts[0];
                    };

                    $ktByKep = [];
                    $kabByKep = [];
                    $order = [];

                    foreach ($tujuan as $i => $tj) {
                        $kep = $getKeperluan($i);

                        if ($tj === 'kelompok_tani') {
                            $namaPoktan = $poktanNama[$i] ?? null;

                            $poktan = $namaPoktan
                                ? \App\Models\Poktan::where('nama_poktan', $namaPoktan)->first()
                                : null;

                            if ($poktan) {
                                if (!isset($ktByKep[$kep])) {
                                    $ktByKep[$kep] = [];
                                    $order[] = ['type' => 'kt', 'kep' => $kep];
                                }

                                $ktByKep[$kep][] =
                                    'KT. ' .
                                    $poktan->nama_poktan .
                                    ' Desa ' .
                                    $poktan->desa .
                                    ' Kecamatan ' .
                                    $poktan->kecamatan;
                            }
                        } elseif ($tj === 'kabupaten_kota') {
                            $kota = trim((string) ($deskripsiKota[$i] ?? ''));
                            if ($kota !== '' && $kota !== '-') {
                                if (!isset($kabByKep[$kep])) {
                                    $kabByKep[$kep] = [];
                                    $order[] = ['type' => 'kab', 'kep' => $kep];
                                }
                                $kabByKep[$kep][] = $kota;
                            }
                        } elseif ($tj === 'lainnya') {
                            $lain = trim((string) ($deskripsiLainnya[$i] ?? ''));
                            if ($lain !== '' && $lain !== '-') {
                                $order[] = ['type' => 'lain', 'val' => $lain];
                            }
                        }
                    }

                    $segments = [];
                    foreach ($order as $o) {
                        if ($o['type'] === 'kt') {
                            $kep = $o['kep'];
                            $segments[] = $kep . ' ke ' . implode(' dan ', $ktByKep[$kep]) . ' Kabupaten Sumenep';
                        }
                        if ($o['type'] === 'kab') {
                            $kep = $o['kep'];
                            $segments[] = $kep . ' ke ' . implode(' dan ', $kabByKep[$kep]);
                        }
                        if ($o['type'] === 'lain') {
                            $segments[] = $o['val'];
                        }
                    }

                    $finalNarasi = trim(implode(' dan ', $segments));
                @endphp

                Biaya bantuan untuk melaksanakan kegiatan
                {{ $finalNarasi }}
                pada {{ $waktuRingkas }},
                sesuai SPT Nomor : {{ $spt->nomor_surat }}
                dan SPD terkait, dengan perincian terlampir.
            </td>
        </tr>

    </table>

    <!-- TTD ATAS -->
    <div style="width:100%; margin-top:40px; text-align:right;">
        Sumenep, {{ now()->translatedFormat('d F Y') }}<br>
        Yang Menerima
    </div>

    <!-- NAMA PENERIMA -->
    <div style="width:100%; margin-top:70px; text-align:right;">
        <b>{{ strtoupper($penerima->nama ?? '-') }}</b><br>
        NIP. {{ $penerima->nip ?? '-' }}
    </div>

    <!-- TTD BAWAH -->
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

    <table style="width:100%; margin-top:50px;">
        <tr>
            <td style="width:50%; vertical-align:top;">
                Setuju dibayar,<br>
                An. Kuasa Pengguna Anggaran<br>
                Pejabat Pembuat Komitmen<br>
                <div class="nama">{{ strtoupper($namaPejabat) }}</div>
                <div class="nip" style="white-space: pre;">NIP. {{ $nipPejabat }}</div>


            </td>
            <td style="width:50%; vertical-align:top; text-align:right;"><br>
                Lunas dibayar, Tgl........................................<br>
                Bendahara Pengeluaran<br>
                <div class="nama">{{ strtoupper($namaBendahara) }}</div>
                <div class="nip" style="white-space: pre;">NIP. {{ $nipBendahara }}</div>
            </td>
        </tr>
    </table>

</div>
