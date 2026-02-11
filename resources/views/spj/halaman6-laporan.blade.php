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

        // ===== TUJUAN (ringkas seperti contoh) =====
        $tujuanLines = [];
        if (is_array($spt->tujuan)) {
            foreach ($spt->tujuan as $i => $tj) {
                if ($tj === 'kelompok_tani') {
                    $nama = $spt->poktan_nama[$i] ?? '-';
                    $tujuanLines[] = str_starts_with(strtoupper(trim($nama)), 'KT.') ? $nama : 'KT. ' . $nama;
                } elseif ($tj === 'kabupaten_kota') {
                    $tujuanLines[] = $spt->deskripsi_kota[$i] ?? '-';
                } elseif ($tj === 'lainnya') {
                    $tujuanLines[] = $spt->deskripsi_lainnya[$i] ?? '-';
                }
            }
        }

        // ===== MAKSUD & TUJUAN (sama seperti halaman SPT) =====
        $tanggalNarasi = $tglBerangkat ? $tglBerangkat->translatedFormat('d F Y') : '-';
        $listKT = [];
        $narasiLain = [];

        if (is_array($spt->tujuan)) {
            foreach ($spt->tujuan as $i => $tj) {
                if ($tj === 'kelompok_tani') {
                    $poktan = \App\Models\Poktan::where('nama_poktan', $spt->poktan_nama[$i] ?? null)->first();
                    $listKT[] =
                        'KT. ' .
                        ($poktan->nama_poktan ?? ($spt->poktan_nama[$i] ?? '-')) .
                        ' Desa ' .
                        ($poktan->desa ?? '-') .
                        ' Kecamatan ' .
                        ($poktan->kecamatan ?? '-');
                } elseif ($tj === 'kabupaten_kota') {
                    $narasiLain[] = ($spt->keperluan ?? '-') . ' ke ' . ($spt->deskripsi_kota[$i] ?? '-');
                } elseif ($tj === 'lainnya') {
                    $narasiLain[] = ($spt->keperluan ?? '-') . ' ' . ($spt->deskripsi_lainnya[$i] ?? '-');
                }
            }
        }

        $narasiKT = null;
        if (count($listKT) > 0) {
            $narasiKT = ($spt->keperluan ?? '-') . ' ke ' . implode(' dan ', $listKT) . ' Kabupaten Sumenep';
        }

        $finalNarasi = array_filter(array_merge([$narasiKT], $narasiLain));
        $maksudTujuanText = count($finalNarasi)
            ? ucfirst(implode(' dan ', $finalNarasi)) . ' pada ' . $tanggalNarasi . '.'
            : '-';

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
