<div class="page">

    @include('spj.partials.kop')

    <div class="judul">
        <h3>SURAT PERINTAH TUGAS</h3>
        <p>Nomor : {{ $spt->nomor_surat }}</p>
    </div>

    <table>
        <tr>
            <td class="label">DASAR</td>
            <td>:</td>
            <td class="isi">
                Surat Perintah dari Kuasa Pengguna Anggaran Pejabat Pembuat Komitmen
                Dinas Ketahanan Pangan dan Pertanian Kabupaten Sumenep
            </td>
        </tr>
    </table>

    <div class="bagian">MEMERINTAHKAN</div>

    <table>
        <tr>
            <td class="label">KEPADA</td>
            <td>:</td>
            <td></td>
        </tr>
    </table>

    <table style="margin-left:25px">
        @forelse ($spt->petugas as $index => $nip)
            @php
                $petugas = \App\Models\Petugas::where('nip', $nip)->first();
            @endphp

            @if ($petugas)
                <tr>
                    <td>{{ $index + 1 }}.</td>
                    <td>Nama</td>
                    <td>:</td>
                    <td><b>{{ strtoupper($petugas->nama) }}</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ $petugas->nip }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Pangkat/Gol</td>
                    <td>:</td>
                    <td>{{ $petugas->pangkat }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $petugas->jabatan }}</td>
                </tr>
                <tr>
                    <td colspan="4" height="8"></td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="4"><i>Tidak ada data petugas</i></td>
            </tr>
        @endforelse
    </table>




    <table style="margin-top:12px; width:100%">
        <tr>
            <td class="label">UNTUK</td>
            <td>:</td>
            <td class="isi">
                @php
                    $tanggal = \Carbon\Carbon::parse($spt->tanggal_berangkat)->translatedFormat('d F Y');

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

                    // Keperluan dipisah pakai ;
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
                            $segments[] = $o['val']; // langsung
                        }
                    }

                    // ✅ gabung antar tujuan pakai "dan"
                    $finalNarasi = trim(implode(' dan ', $segments));
                @endphp

                {{ ucfirst($finalNarasi) }} pada {{ $tanggal }}.
                <br><br>
                Demikian surat tugas ini dibuat untuk dipergunakan dengan sebaik-baiknya.
            </td>
        </tr>
    </table>




    <br>

    @php
        // Ambil user login (pejabat)
        $pejabat = $user ?? auth()->user();

        $namaPejabat = $pejabat->nama ?? ($pejabat->name ?? '-');
        $nipPejabat = $pejabat->nip ?? '-';
        $jabatanPejabat = $pejabat->jabatan ?? 'Pejabat Pembuat Komitmen';
    @endphp

    <div class="ttd">
        Dikeluarkan di : Sumenep<br>
        Tanggal : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><br>

        An. Kuasa Pengguna Anggaran<br>
        Pejabat Pembuat Komitmen<br>

        <div class="nama">{{ strtoupper($namaPejabat) }}</div>
        <div class="nip" style="white-space: pre;">NIP. {{ $nipPejabat }}</div>
    </div>


</div>
