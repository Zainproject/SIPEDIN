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

                    $listKT = [];
                    $narasiLain = [];

                    if (is_array($spt->tujuan)) {
                        foreach ($spt->tujuan as $i => $tj) {
                            // 1. KELOMPOK TANI (SUMENEP)
                            if ($tj === 'kelompok_tani') {
                                $poktan = \App\Models\Poktan::where(
                                    'nama_poktan',
                                    $spt->poktan_nama[$i] ?? null,
                                )->first();

                                $listKT[] =
                                    'KT. ' .
                                    ($poktan->nama_poktan ?? '-') .
                                    ' Desa ' .
                                    ($poktan->desa ?? '-') .
                                    ' Kecamatan ' .
                                    ($poktan->kecamatan ?? '-');
                            }

                            // 2. KABUPATEN / KOTA LAIN
                            elseif ($tj === 'kabupaten_kota') {
                                $narasiLain[] = $spt->keperluan . ' ke ' . ($spt->deskripsi_kota[$i] ?? '-');
                            }

                            // 3. LAINNYA
                            elseif ($tj === 'lainnya') {
                                $narasiLain[] = $spt->keperluan . ' ' . ($spt->deskripsi_lainnya[$i] ?? '-');
                            }
                        }
                    }

                    // Gabung KT (1 atau 2) → Kabupaten Sumenep sekali saja
                    if (count($listKT) > 0) {
                        $narasiKT = $spt->keperluan . ' ke ' . implode(' dan ', $listKT) . ' Kabupaten Sumenep';
                    } else {
                        $narasiKT = null;
                    }

                    // Gabung semua narasi
                    $finalNarasi = array_filter(array_merge([$narasiKT], $narasiLain));
                @endphp

                {{ ucfirst(implode(' dan ', $finalNarasi)) }}
                pada {{ $tanggal }}.
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
