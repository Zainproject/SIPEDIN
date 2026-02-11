<?php

namespace App\Http\Controllers;

use App\Models\Spt;
use App\Models\Petugas;
use App\Models\Poktan;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $bulanIni = (int) $now->month;
        $tahunIni = (int) $now->year;

        // =========================
        // KARTU DASHBOARD
        // =========================

        // SPT bulan ini
        $jumlahSptBulanIni = Spt::where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->count();

        // Total biaya bulan ini
        $totalBiayaBulanIni = (int) Spt::where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->sum('total_biaya');

        // SPT sedang berjalan hari ini (range tanggal)
        $sptBerjalan = Spt::whereDate('tanggal_berangkat', '<=', $now->toDateString())
            ->whereDate('tanggal_kembali', '>=', $now->toDateString())
            ->get(['petugas']);

        $jumlahSptBerjalan = $sptBerjalan->count();

        // Hitung petugas yang sedang bertugas (unique NIP dari JSON petugas)
        $nipBerangkat = [];
        foreach ($sptBerjalan as $row) {
            $arr = is_array($row->petugas) ? $row->petugas : (json_decode($row->petugas, true) ?: []);
            foreach ($arr as $nip) {
                $nip = (string) $nip;
                if ($nip !== '') {
                    $nipBerangkat[$nip] = true;
                }
            }
        }
        $jumlahPetugasBerangkat = count($nipBerangkat);

        // Master data
        $jumlahPetugas = Petugas::count();
        $jumlahPoktan  = Poktan::count();

        // =========================
        // GRAFIK 1: BIAYA PER BULAN (JAN–DES) TAHUN INI
        // =========================
        $biayaPerBulan = Spt::selectRaw('bulan, COALESCE(SUM(total_biaya),0) as total')
            ->where('tahun', $tahunIni)
            ->whereNotNull('bulan')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $dataBiayaBulanan = [];

        for ($m = 1; $m <= 12; $m++) {
            $dataBiayaBulanan[] = (int) ($biayaPerBulan[$m]->total ?? 0);
        }

        $chartBiayaBulanan = [
            'labels' => $labelBulan,
            'biaya'  => $dataBiayaBulanan,
            'tahun'  => $tahunIni,
        ];

        // =========================
        // GRAFIK 2: STATUS SPT (SELESAI vs BELUM)
        // - selesai: tanggal_kembali < hari ini
        // - belum:   tanggal_kembali >= hari ini
        // =========================
        $sptSelesai = Spt::whereDate('tanggal_kembali', '<', $now->toDateString())->count();
        $sptBelum   = Spt::whereDate('tanggal_kembali', '>=', $now->toDateString())->count();

        $chartStatus = [
            'labels' => ['SPT Selesai', 'SPT Belum Selesai'],
            'data'   => [(int) $sptSelesai, (int) $sptBelum],
        ];

        // =========================
        // RETURN VIEW
        // =========================
        return view('layout.main', compact(
            'jumlahSptBulanIni',
            'totalBiayaBulanIni',
            'jumlahSptBerjalan',
            'jumlahPetugasBerangkat',
            'jumlahPetugas',
            'jumlahPoktan',
            'chartBiayaBulanan',
            'chartStatus'
        ));
    }
}
