<div class="page sppd-wrap">
    @php
        use Carbon\Carbon;

        // ====== DATA SPT ======
        $tempatAwal = trim((string) ($spt->berangkat_dari ?? ''));
        if ($tempatAwal === '') {
            $tempatAwal = 'BPP Gapura';
        }

        $alatAngkut = trim((string) ($spt->alat_angkut ?? '-'));

        $tglBerangkatObj = $spt->tanggal_berangkat ? Carbon::parse($spt->tanggal_berangkat) : null;
        $tglKembaliObj = $spt->tanggal_kembali ? Carbon::parse($spt->tanggal_kembali) : null;

        $tglBerangkat = $tglBerangkatObj ? $tglBerangkatObj->translatedFormat('d F Y') : '';
        $tglKembali = $tglKembaliObj ? $tglKembaliObj->translatedFormat('d F Y') : '';

        // Di gambar, semua blok pakai tanggal berangkat
        $tgl = $tglBerangkat;

        // ====== LIST TUJUAN MAKS 2 ======
        $tujuanArr = is_array($spt->tujuan) ? array_values($spt->tujuan) : [];
        $poktanArr = is_array($spt->poktan_nama) ? array_values($spt->poktan_nama) : [];
        $kotaArr = is_array($spt->deskripsi_kota) ? array_values($spt->deskripsi_kota) : [];
        $lainArr = is_array($spt->deskripsi_lainnya) ? array_values($spt->deskripsi_lainnya) : [];

        $norm = fn($v) => trim((string) ($v ?? ''));

        $destinations = [];

        foreach ($tujuanArr as $i => $tj) {
            $tj = $norm($tj);
            if ($tj === '') {
                continue;
            }

            if ($tj === 'kelompok_tani') {
                $namaPoktan = $norm($poktanArr[$i] ?? '');
                if ($namaPoktan === '') {
                    continue;
                }

                $pt = \App\Models\Poktan::where('nama_poktan', $namaPoktan)->first();

                $destinations[] = [
                    'label' => 'KT. ' . ($pt?->nama_poktan ?? $namaPoktan),
                    'ketua' => $pt?->ketua ?? '', // ketua poktan
                    'jenis' => 'poktan',
                ];
            } elseif ($tj === 'kabupaten_kota') {
                $kota = $norm($kotaArr[$i] ?? '');
                if ($kota === '') {
                    continue;
                }

                $destinations[] = [
                    'label' => $kota,
                    'ketua' => '', // bukan poktan
                    'jenis' => 'kota',
                ];
            } elseif ($tj === 'lainnya') {
                $lain = $norm($lainArr[$i] ?? '');
                if ($lain === '') {
                    continue;
                }

                $destinations[] = [
                    'label' => $lain,
                    'ketua' => '', // bukan poktan
                    'jenis' => 'lainnya',
                ];
            }
        }

        $destinations = collect($destinations)->unique(fn($d) => $d['label'])->values()->take(2)->all();

        $countTujuan = count($destinations);

        $d1 = $destinations[0] ?? ['label' => '', 'ketua' => '', 'jenis' => ''];
        $d2 = $destinations[1] ?? ['label' => '', 'ketua' => '', 'jenis' => ''];

        $tujuan1 = $d1['label'];
        $tujuan2 = $d2['label'];

        // ====== KETUA: poktan -> nama ketua + "Ketua", selain itu titik saja ======
        $dotsKetua = '................................';

        $isPoktan1 = $d1['jenis'] === 'poktan';
        $isPoktan2 = $d2['jenis'] === 'poktan';

        $ketua1 = $isPoktan1 ? (trim((string) ($d1['ketua'] ?? '')) ?: $dotsKetua) : $dotsKetua;
        $ketua2 = $isPoktan2 ? (trim((string) ($d2['ketua'] ?? '')) ?: $dotsKetua) : $dotsKetua;

        $tujuanSetelah1 = $tujuan2 ?: $tempatAwal;

        // ====== NOMOR ======
        $noI = 'I.';
        $noII = 'II.';
        $noIII = 'III.';
        $noIV = 'IV.';
        $noV = 'V.';
        $noVI = 'VI.';
        $noVII = 'VII.';
        $noVIII = 'VIII.';

        if ($countTujuan < 2) {
            $noIII = 'III.'; // dipakai untuk ROW IV (kosong)
            $noIV = 'IV.'; // dipakai untuk ROW V (kosong)
            $noV = 'V.'; // dipakai untuk ROW VI (penutup)
            $noVI = 'VI.'; // Catatan Lain-lain
            $noVII = 'VII.'; // PERHATIAN
            $noVIII = 'VIII.'; // (tetap sesuai layout kamu)
        }
    @endphp

    <style>
        .sppd-wrap table {
            border-collapse: collapse;
        }

        .sppd-wrap td {
            vertical-align: top;
            font-size: 11pt;
        }

        .sppd-wrap .main-table>tbody>tr>td {
            padding: 10px 12px;
        }

        .sppd-wrap .blank-cell {
            padding: 0 !important;
        }

        .sppd-wrap .mid {
            width: 5%;
        }

        .sppd-wrap .lbl {
            width: 45%;
        }

        .sppd-wrap .no {
            width: 6%;
        }

        .sppd-wrap .ttd-center {
            text-align: center;
            line-height: 1.15;
        }

        /* Supaya posisi nama/ketua seperti gambar (agak ke bawah dan center) */
        .sppd-wrap .ttd-spacer {
            height: 36px;
        }

        .sppd-wrap .dots {
            text-align: center;
            margin-top: 26px;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }

        .sppd-wrap .blok-catatan,
        .sppd-wrap .blok-catatan * {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .sppd-wrap .catatan-bintang {
            margin-left: 20px;
            margin-top: 4px;
        }
    </style>

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

    <table class="main-table" width="100%" cellspacing="0" border="1">
        <!-- ===================== ROW I ===================== -->
        <tr>
            <td width="50%" class="blank-cell">&nbsp;</td>

            <td width="50%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="no">{{ $noI }}</td>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="lbl">Berangkat dari <br>Tempat Kedudukan</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tempatAwal }}</td>
                                </tr>

                                <tr>
                                    <td>Ke</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tujuan1 }}</td>
                                </tr>
                                <tr>
                                    <td>Pada tanggal</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tgl }}</td>
                                </tr>
                            </table>

                            An. Kuasa Pengguna Anggaran<br>
                            Pejabat Pembuat Komitmen (PPK) <br>
                            <div class="nama">{{ strtoupper($namaPejabat) }}</div>
                            <div class="nip" style="white-space: pre;">NIP. {{ $nipPejabat }}</div>

                            <div class="ttd-spacer"></div>

                            <div class="ttd-center"></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ===================== ROW II ===================== -->
        <tr>
            <td width="50%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="no">{{ $noII }}</td>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="lbl">Tiba di</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tujuan1 }}</td>
                                </tr>
                                <tr>
                                    <td>Pada Tanggal</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tgl }}</td>
                                </tr>
                                <tr>
                                    <td>Pejabat Berwenang *)</td>
                                    <td class="mid">:</td>
                                    <td></td>
                                </tr>
                            </table>

                            <div class="ttd-spacer"></div>

                            <div class="ttd-center"><br>
                                @if ($isPoktan1)
                                    <b>{{ $ketua1 }}</b><br>
                                    Ketua
                                @else
                                    <div class="dots">{{ $dotsKetua }}</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="50%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="lbl">Berangkat dari</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tujuan1 }}</td>
                                </tr>
                                <tr>
                                    <td>Ke</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tujuanSetelah1 }}</td>
                                </tr>
                                <tr>
                                    <td>Pada tanggal</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tgl }}</td>
                                </tr>
                                <tr>
                                    <td>Pejabat Berwenang *)</td>
                                    <td class="mid">:</td>
                                    <td></td>
                                </tr>
                            </table>

                            <div class="ttd-spacer"></div>

                            <div class="ttd-center">
                                @if ($isPoktan1)
                                    <b>{{ $ketua1 }}</b><br>
                                    Ketua
                                @else
                                    <div class="dots">{{ $dotsKetua }}</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ===================== ROW III (JIKA ADA TUJUAN 2) ===================== -->
        @if ($countTujuan >= 2)
            <tr>
                <td width="50%">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="no">{{ 'III.' }}</td>
                            <td>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="lbl">Tiba di</td>
                                        <td class="mid">:</td>
                                        <td>{{ $tujuan2 }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pada Tanggal</td>
                                        <td class="mid">:</td>
                                        <td>{{ $tgl }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pejabat Berwenang *)</td>
                                        <td class="mid">:</td>
                                        <td></td>
                                    </tr>
                                </table>

                                <div class="ttd-spacer"></div>

                                <div class="ttd-center"><br><br>
                                    @if ($isPoktan2)
                                        <b>{{ $ketua2 }}</b><br>
                                        Ketua
                                    @else
                                        <div class="dots">{{ $dotsKetua }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>

                <td width="50%">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="lbl">Berangkat dari</td>
                                        <td class="mid">:</td>
                                        <td>{{ $tujuan2 }}</td>
                                    </tr>
                                    <tr>
                                        <td>Ke</td>
                                        <td class="mid">:</td>
                                        <td>{{ $tempatAwal }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pada tanggal</td>
                                        <td class="mid">:</td>
                                        <td>{{ $tgl }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pejabat Berwenang *)</td>
                                        <td class="mid">:</td>
                                        <td></td>
                                    </tr>
                                </table>

                                <div class="ttd-spacer"></div>

                                <div class="ttd-center"><br>
                                    @if ($isPoktan2)
                                        <b>{{ $ketua2 }}</b><br>
                                        Ketua
                                    @else
                                        <div class="dots">{{ $dotsKetua }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif

        <!-- ===================== ROW IV (KOSONG) ===================== -->
        <tr>
            <td width="50%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="no">{{ $countTujuan >= 2 ? 'IV.' : $noIII }}</td>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="lbl">Tiba di</td>
                                    <td class="mid">:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Pada Tanggal</td>
                                    <td class="mid">:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Pejabat Berwenang *)</td>
                                    <td class="mid">:</td>
                                    <td></td>
                                </tr>
                            </table><br>
                            <div class="dots">..................................................................</div>
                            NIP.
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="lbl">Berangkat dari</td>
                        <td class="mid">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Ke</td>
                        <td class="mid">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Pada tanggal</td>
                        <td class="mid">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Pejabat Berwenang *)</td>
                        <td class="mid">:</td>
                        <td></td>
                    </tr>
                </table>
                <div class="dots">..................................................................</div>
                NIP.
            </td>
        </tr>

        <!-- ===================== ROW V (KOSONG) ===================== -->
        <tr>
            <td width="50%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="no">{{ $countTujuan >= 2 ? 'V.' : $noIV }}</td>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="lbl">Tiba di</td>
                                    <td class="mid">:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Pada Tanggal</td>
                                    <td class="mid">:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Pejabat Berwenang *)</td>
                                    <td class="mid">:</td>
                                    <td></td>
                                </tr>
                            </table><br>
                            <div class="dots">..................................................................
                            </div>
                            NIP.
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="lbl">Berangkat dari</td>
                        <td class="mid">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Ke</td>
                        <td class="mid">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Pada tanggal</td>
                        <td class="mid">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Pejabat Berwenang *)</td>
                        <td class="mid">:</td>
                        <td></td>
                    </tr>
                </table>
                <div class="dots">..................................................................</div>
                NIP.
            </td>
        </tr>

        <!-- ===================== ROW VI ===================== -->
        <tr>
            <td width="50%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="no">{{ $countTujuan >= 2 ? 'VI.' : $noV }}</td>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="lbl">Tiba di</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tempatAwal }}</td>
                                </tr>
                                <tr>
                                    <td>(Tempat kedudukan)</td>
                                    <td></td>
                                    <td>{{ $tempatAwal }}</td>
                                </tr>
                                <tr>
                                    <td>Pada tanggal</td>
                                    <td class="mid">:</td>
                                    <td>{{ $tglKembali ?: $tgl }}</td>
                                </tr>
                            </table>

                            <br>
                            An. Kuasa Pengguna Anggaran<br>
                            Pejabat Pembuat Komitmen (PPK) <br>
                            <div class="nama">{{ strtoupper($namaPejabat) }}</div>
                            <div class="nip" style="white-space: pre;">NIP. {{ $nipPejabat }}</div>

                            <div class="ttd-spacer"></div>

                            <div class="ttd-center"></div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="50%">
                Telah diperiksa dengan keterangan bahwa perjalanan tersebut
                atas perintahnya dan semata-mata untuk kepentingan jabatan
                dalam waktu yang sesingkat-singkatnya.<br><br>

                An. Kuasa Pengguna Anggaran<br>
                Pejabat Pembuat Komitmen (PPK) <br>
                <div class="nama">{{ strtoupper($namaPejabat) }}</div>
                <div class="nip" style="white-space: pre;">NIP. {{ $nipPejabat }}</div>

                <div class="ttd-spacer"></div>

                <div class="ttd-center"></div>
            </td>
        </tr>

        <!-- ===================== VII & VIII ===================== -->
        <tbody class="blok-catatan">
            <tr>
                <td colspan="2">{{ $countTujuan >= 2 ? 'VII.' : $noVI }}&nbsp;&nbsp;&nbsp;Catatan Lain-lain</td>
            </tr>
            <tr>
                <td colspan="2">{{ $countTujuan >= 2 ? 'VIII.' : $noVII }}&nbsp;&nbsp;&nbsp;PERHATIAN :</td>
            </tr>
            <tr>
                <td colspan="2">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Pejabat yang berwewenang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat
                    yang mengesahkan tanggal berangkat / tiba serta bendaharawan bertanggung jawab berdasarkan
                    peraturan - peraturan keuangan negara, apabila negara menderita rugi akibat kesalahan, kelalaian
                    dan kealpaannya.
                </td>
            </tr>
        </tbody>
    </table>

    <div class="catatan-bintang">
        *) Pejabat pada instansi yang dituju
    </div>
</div>
