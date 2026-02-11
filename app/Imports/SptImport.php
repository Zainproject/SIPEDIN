<?php

namespace App\Imports;

use App\Models\Spt;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SptImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // =========================
            // Helper ambil string aman
            // =========================
            $get = function ($key, $default = '') use ($row) {
                $v = $row[$key] ?? $default;
                if (is_null($v)) return $default;
                return trim((string) $v);
            };

            // =========================
            // Helper parse list (dipisah koma/;| )
            // =========================
            $parseList = function ($text) {
                $text = trim((string) $text);
                if ($text === '') return [];
                $text = str_replace([';', '|'], ',', $text);
                return array_values(array_filter(array_map('trim', explode(',', $text))));
            };

            // =========================
            // Helper parse tanggal
            // =========================
            $parseDate = function ($val) {
                if ($val === null || $val === '') return null;

                // excel numeric date
                if (is_numeric($val)) {
                    try {
                        return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val))
                            ->format('Y-m-d');
                    } catch (\Throwable $e) {
                        return null;
                    }
                }

                try {
                    return Carbon::parse($val)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return null;
                }
            };

            // =========================
            // Romawi bulan untuk format nomor surat
            // =========================
            $romawi = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII'
            ];

            // =========================
            // Ambil field dasar dari excel
            // =========================
            $tanggalBerangkat = $parseDate($row['tanggal_berangkat'] ?? '');
            $tanggalKembali   = $parseDate($row['tanggal_kembali'] ?? '');

            // fallback kalau kosong, biar tidak error
            $today = now()->format('Y-m-d');
            if (!$tanggalBerangkat) $tanggalBerangkat = $today;
            if (!$tanggalKembali) $tanggalKembali = $today;

            $start = Carbon::parse($tanggalBerangkat);
            $end   = Carbon::parse($tanggalKembali);

            $lamaHari = $end->diffInDays($start) + 1;
            if ($lamaHari < 1) $lamaHari = 1;

            $bulan = (int) ($row['bulan'] ?? $start->month);
            $tahun = (int) ($row['tahun'] ?? $start->year);
            if ($bulan < 1 || $bulan > 12) $bulan = $start->month;
            if ($tahun < 2000) $tahun = $start->year;

            $bulanRomawi = $romawi[$bulan] ?? (string) $bulan;

            // =========================
            // PETUGAS (NIP list)
            // kolom excel: petugas (contoh: 19870001,19870002)
            // =========================
            $petugasArr = $parseList($get('petugas'));

            // =========================
            // TUJUAN list
            // kolom excel: tujuan (contoh: kabupaten_kota,kelompok_tani,lainnya)
            // =========================
            $tujuanArr = $parseList($get('tujuan'));

            // =========================
            // Poktan list (nama poktan)
            // kolom excel: poktan_nama
            // =========================
            $poktanArr = $parseList($get('poktan_nama'));

            // =========================
            // Deskripsi kota/lainnya (bisa list juga)
            // kolom excel: deskripsi_kota, deskripsi_lainnya
            // =========================
            $deskripsiKotaArr = $parseList($get('deskripsi_kota'));
            $deskripsiLainnyaArr = $parseList($get('deskripsi_lainnya'));

            // =========================
            // BIAYA multi item
            // kolom excel: keterangan_biaya, harga_biaya
            // contoh:
            // keterangan_biaya: Uang harian,BBM
            // harga_biaya: 150000,50000
            // =========================
            $ketBiaya = $parseList($get('keterangan_biaya'));
            $hargaBiayaRaw = $parseList($get('harga_biaya'));

            $hargaBiaya = [];
            foreach ($hargaBiayaRaw as $hb) {
                $hb = preg_replace('/[^0-9]/', '', (string) $hb);
                $hargaBiaya[] = (int) ($hb === '' ? 0 : $hb);
            }

            // minimal 1 biaya (meniru form)
            if (count($ketBiaya) === 0) $ketBiaya = ['Uang harian'];
            if (count($hargaBiaya) === 0) $hargaBiaya = [0];

            // samakan panjang array
            $max = max(count($ketBiaya), count($hargaBiaya));
            $ketBiaya = array_pad($ketBiaya, $max, end($ketBiaya));
            $hargaBiaya = array_pad($hargaBiaya, $max, (int) end($hargaBiaya));

            $subtotalPerHari = array_sum($hargaBiaya);
            $totalBiaya = $subtotalPerHari * $lamaHari;

            // =========================
            // STEP 4 arrays
            // kolom excel: arahan, masalah, saran, lainnya
            // bisa dipisah koma
            // =========================
            $arahanArr  = $parseList($get('arahan'));
            $masalahArr = $parseList($get('masalah'));
            $saranArr   = $parseList($get('saran'));
            $lainnyaArr = $parseList($get('lainnya'));

            // minimal 1 item (meniru form yang required)
            if (count($arahanArr) === 0) $arahanArr = ['-'];
            if (count($masalahArr) === 0) $masalahArr = ['-'];
            if (count($saranArr) === 0) $saranArr = ['-'];
            if (count($lainnyaArr) === 0) $lainnyaArr = ['-'];

            // =========================
            // Field wajib yang bikin error: KEHADIRAN
            // kolom excel: kehadiran
            // =========================
            $kehadiran = $get('kehadiran', '');
            if ($kehadiran === '') {
                // fallback biar tidak null
                $kehadiran = 'Hadir';
            }

            // =========================
            // Nomor surat & kwitansi
            // excel bisa isi nomor awal saja (contoh 090)
            // kolom excel: nomor_surat_awal, nomor_kwitansi_awal
            // atau langsung nomor_surat, nomor_kwitansi
            // =========================
            $nomorSurat = $get('nomor_surat', '');
            $nomorKwitansi = $get('nomor_kwitansi', '');

            if ($nomorSurat === '') {
                $awal = $get('nomor_surat_awal', '');
                if ($awal !== '') {
                    $nomorSurat = $awal . '/spt/500.0/' . $bulanRomawi . '/' . $tahun;
                } else {
                    $nomorSurat = '000/spt/500.0/' . $bulanRomawi . '/' . $tahun;
                }
            }

            if ($nomorKwitansi === '') {
                $awal = $get('nomor_kwitansi_awal', '');
                if ($awal !== '') {
                    $nomorKwitansi = $awal . '/spt/500.0/' . $bulanRomawi . '/' . $tahun;
                } else {
                    $nomorKwitansi = 'KW-000';
                }
            }

            // =========================
            // SIMPAN
            // =========================
            Spt::create([
                'petugas' => $petugasArr,
                'tujuan' => $tujuanArr,
                'poktan_nama' => $poktanArr,
                'deskripsi_kota' => $deskripsiKotaArr,
                'deskripsi_lainnya' => $deskripsiLainnyaArr,

                'keperluan' => $get('keperluan', 'Observasi Lapangan'),
                'kehadiran' => $kehadiran,

                'alat_angkut' => $get('alat_angkut', '-'),
                'berangkat_dari' => $get('berangkat_dari', '-'),

                'tanggal_berangkat' => $tanggalBerangkat,
                'tanggal_kembali' => $tanggalKembali,

                'keterangan_biaya' => $ketBiaya,
                'harga_biaya' => $hargaBiaya,
                'lama_hari' => $lamaHari,
                'subtotal_perhari' => $subtotalPerHari,
                'total_biaya' => $totalBiaya,

                'bulan' => $bulan,
                'tahun' => $tahun,
                'mak' => $get('mak', '001'),

                'nomor_surat' => $nomorSurat,
                'nomor_kwitansi' => $nomorKwitansi,

                'arahan' => $arahanArr,
                'masalah' => $masalahArr,
                'saran' => $saranArr,
                'lainnya' => $lainnyaArr,
            ]);
        }
    }
}
