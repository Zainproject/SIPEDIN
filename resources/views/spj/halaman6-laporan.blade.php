<div class="page">

    <center>
        <b><u>LAPORAN PERJALANAN DINAS</u></b>
    </center>

    <br>

    @php
        use Carbon\Carbon;

        // ===== TANGGAL =====
        $tglDasar = $spt->tanggal_berangkat ? Carbon::parse($spt->tanggal_berangkat)->translatedFormat('d F Y') : '-';

        $tglBerangkat = $spt->tanggal_berangkat ? Carbon::parse($spt->tanggal_berangkat) : null;
        $tglKembali = $spt->tanggal_kembali ? Carbon::parse($spt->tanggal_kembali) : null;

        $waktuPelaksanaan = '-';
        if ($tglBerangkat && $tglKembali) {
            $waktuPelaksanaan = $tglBerangkat->equalTo($tglKembali)
                ? $tglBerangkat->translatedFormat('d F Y')
                : $tglBerangkat->translatedFormat('d F Y') . ' s/d ' . $tglKembali->translatedFormat('d F Y');
        } elseif ($tglBerangkat) {
            $waktuPelaksanaan = $tglBerangkat->translatedFormat('d F Y');
        }

        // ===== PETUGAS =====
        $petugasList = $spt->petugasList();

        // ===== NORMALISASI (AMAN JSON/ARRAY/CSV) =====
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

                // fallback csv (pakai ; atau |)
                $val = str_replace(['|'], ';', $val);
                return array_values(array_filter(array_map('trim', explode(';', $val))));
            }

            return [];
        };

        $tujuan = $norm($spt->tujuan);
        $poktanNama = $norm($spt->poktan_nama);
        $deskripsiKota = $norm($spt->deskripsi_kota);
        $deskripsiLainnya = $norm($spt->deskripsi_lainnya);

        // ===== TUJUAN (ringkas seperti contoh) =====
        $tujuanLines = [];
        foreach ($tujuan as $i => $tj) {
            if ($tj === 'kelompok_tani') {
                $nama = $poktanNama[$i] ?? '-';
                $tujuanLines[] = str_starts_with(strtoupper(trim($nama)), 'KT.') ? $nama : 'KT. ' . $nama;
            } elseif ($tj === 'kabupaten_kota') {
                $tujuanLines[] = $deskripsiKota[$i] ?? '-';
            } elseif ($tj === 'lainnya') {
                $tujuanLines[] = $deskripsiLainnya[$i] ?? '-';
            }
        }

        // ===== MAKSUD & TUJUAN (SAMA DENGAN HALAMAN SPT/UNTUK) =====
        $tanggalNarasi = $tglBerangkat ? $tglBerangkat->translatedFormat('d F Y') : '-';

        // keperluan dipisah pakai ;
        $keperluanParts = array_values(array_filter(array_map('trim', explode(';', (string) ($spt->keperluan ?? '')))));
        $getKeperluan = function ($i) use ($keperluanParts, $spt) {
            if (count($keperluanParts) === 0) {
                return (string) ($spt->keperluan ?? '-');
            }
            return $keperluanParts[$i] ?? $keperluanParts[0];
        };

        // grouping + urutan
        $ktByKep = [];
        $kabByKep = [];
        $order = [];

        foreach ($tujuan as $i => $tj) {
            $kep = $getKeperluan($i);

            if ($tj === 'kelompok_tani') {
                $namaPoktan = $poktanNama[$i] ?? null;

                $poktan = $namaPoktan ? \App\Models\Poktan::where('nama_poktan', $namaPoktan)->first() : null;

                if ($poktan) {
                    if (!isset($ktByKep[$kep])) {
                        $ktByKep[$kep] = [];
                        $order[] = ['type' => 'kt', 'kep' => $kep];
                    }

                    $ktByKep[$kep][] =
                        'KT. ' . $poktan->nama_poktan . ' Desa ' . $poktan->desa . ' Kecamatan ' . $poktan->kecamatan;
                } else {
                    // fallback kalau poktan tidak ketemu
                    if (!isset($ktByKep[$kep])) {
                        $ktByKep[$kep] = [];
                        $order[] = ['type' => 'kt', 'kep' => $kep];
                    }
                    $ktByKep[$kep][] = 'KT. ' . ($namaPoktan ?? '-');
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
                $segments[] = $o['val']; // langsung
            }
        }

        $finalNarasiStr = trim(implode(' dan ', $segments));

        $maksudTujuanText = $finalNarasiStr !== '' ? ucfirst($finalNarasiStr) . ' pada ' . $tanggalNarasi . '.' : '-';

        // ===== STEP 4: buang kosong & buang "-" supaya tidak jadi "1. -" =====
        $cleanList = function ($arr) {
            if (!is_array($arr)) {
                return [];
            }
            return array_values(
                array_filter($arr, function ($v) {
                    $v = trim((string) $v);
                    return $v !== '' && $v !== '-' && $v !== '—';
                }),
            );
        };

        $arahan = $cleanList($spt->arahan ?? []);
        $masalah = $cleanList($spt->masalah ?? []);
        $saran = $cleanList($spt->saran ?? []);
        $lainnya = $cleanList($spt->lainnya ?? []);
    @endphp

    <table width="100%" cellpadding="2" cellspacing="0">
        <tr>
            <td width="5%">I.</td>
            <td width="30%">Dasar</td>
            <td width="2%">:</td>
            <td>
                SPT {{ $spt->nomor_surat ?? '-' }}<br>
                Tanggal : {{ $tglDasar }}
            </td>
        </tr>

        <tr>
            <td>II.</td>
            <td>Maksud dan Tujuan</td>
            <td>:</td>
            <td>
                {{ $maksudTujuanText }}
            </td>
        </tr>

        <tr>
            <td>III.</td>
            <td>Waktu Pelaksanaan</td>
            <td>:</td>
            <td>Tanggal {{ $waktuPelaksanaan }}</td>
        </tr>

        <tr>
            <td>IV.</td>
            <td>Nama Petugas / NIP</td>
            <td>:</td>
            <td>
                @if ($petugasList->count())
                    {{-- TABEL KECIL: bikin layout persis seperti contoh --}}
                    <table width="100%" cellpadding="0" cellspacing="0">
                        @foreach ($petugasList as $i => $p)
                            <tr>
                                <td width="5%">{{ $i + 1 }}.</td>
                                <td width="12%">Nama</td>
                                <td width="2%">:</td>
                                <td width="81%">{{ strtoupper($p->nama) }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>NIP.</td>
                                <td>:</td>
                                <td>{{ $p->nip }}</td>
                            </tr>
                            @if (!$loop->last)
                                <tr>
                                    <td colspan="4" height="8"></td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td>V.</td>
            <td>Daerah tujuan / instansi<br>yang dikunjungi</td>
            <td>:</td>
            <td>
                @if (count($tujuanLines))
                    {{-- mirip contoh: baris kedua diawali "dan" --}}
                    @foreach ($tujuanLines as $i => $t)
                        @if ($i === 0)
                            {{ $t }}<br>
                        @else
                            dan<br>{{ $t }}<br>
                        @endif
                    @endforeach
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td>VI.</td>
            <td>Hadir dalam pertemuan</td>
            <td>:</td>
            <td>{{ trim((string) ($spt->kehadiran ?? '')) !== '' ? $spt->kehadiran : '-' }}</td>
        </tr>

        <tr>
            <td>VII.</td>
            <td>Petunjuk/<br>arahan</td>
            <td>:</td>
            <td>
                @if (count($arahan))
                    @foreach ($arahan as $i => $v)
                        {{ $i + 1 }}. {{ $v }}<br>
                    @endforeach
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td>VIII.</td>
            <td>Masalah/temuan</td>
            <td>:</td>
            <td>
                @if (count($masalah))
                    @foreach ($masalah as $i => $v)
                        {{ $i + 1 }}. {{ $v }}<br>
                    @endforeach
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td>IX.</td>
            <td>Saran tindakan</td>
            <td>:</td>
            <td>
                @if (count($saran))
                    @foreach ($saran as $i => $v)
                        {{ $i + 1 }}. {{ $v }}<br>
                    @endforeach
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td>X.</td>
            <td>Lain-lain</td>
            <td>:</td>
            <td>
                @if (count($lainnya))
                    @foreach ($lainnya as $i => $v)
                        {{ $i + 1 }}. {{ $v }}<br>
                    @endforeach
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <br><br>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="45%"></td>
            <td width="55%">
                Sumenep, {{ $tglDasar }}
                <br><br>
                Pelapor,
                <br><br>

                <table width="100%" cellpadding="2" cellspacing="0">
                    @if ($petugasList->count())
                        @foreach ($petugasList as $i => $p)
                            <tr>
                                <td width="5%">{{ $i + 1 }}.</td>
                                <td width="60%">
                                    {{ strtoupper($p->nama) }}<br>
                                    NIP. {{ $p->nip }}
                                </td>
                                <td width="5%">{{ $i + 1 }}.</td>
                                <td width="30%">..............................</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">-</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

</div>
