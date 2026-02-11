<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Spt;
use App\Models\Petugas;
use App\Models\Poktan;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Halaman awal: tidak tampilkan tabel, hanya UI search
        return view('search.index');
    }

    public function results(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        // Biar tidak berat: minimal 2 karakter (ubah sesuai kebutuhan)
        if (mb_strlen($q) < 2) {
            return response()->json([
                'q' => $q,
                'counts' => ['spt' => 0, 'petugas' => 0, 'poktan' => 0],
                'data' => ['spt' => [], 'petugas' => [], 'poktan' => []],
                'message' => 'Ketik minimal 2 karakter untuk mulai mencari.',
            ]);
        }

        $limit = (int) $request->get('limit', 200);
        $limit = max(10, min($limit, 500)); // batasi biar aman

        /**
         * 1) PETUGAS
         */
        $petugasQuery = Petugas::query()
            ->where('nama', 'like', "%{$q}%");

        if (Schema::hasColumn('petugas', 'nip')) {
            $petugasQuery->orWhere('nip', 'like', "%{$q}%");
        }

        $petugas = $petugasQuery->latest()->limit(50)->get();

        /**
         * 2) POKTAN (tanpa kabupaten_kota)
         */
        $poktanQuery = Poktan::query()
            ->where('nama_poktan', 'like', "%{$q}%")
            ->orWhere('desa', 'like', "%{$q}%")
            ->orWhere('kecamatan', 'like', "%{$q}%");

        $possibleKetuaCols = ['ketua', 'ketua_poktan', 'nama_ketua', 'ketua_nama'];
        foreach ($possibleKetuaCols as $col) {
            if (Schema::hasColumn('poktan', $col)) {
                $poktanQuery->orWhere($col, 'like', "%{$q}%");
            }
        }

        $poktan = $poktanQuery->latest()->limit(50)->get();

        /**
         * 3) Tujuan keywords
         */
        $qLower = mb_strtolower($q);
        $tujuanKeys = [];

        if (str_contains($qLower, 'kabupaten') || str_contains($qLower, 'kota')) {
            $tujuanKeys[] = 'kabupaten_kota';
        }

        if (str_contains($qLower, 'poktan') || str_contains($qLower, 'kelompok tani') || str_contains($qLower, 'tani')) {
            $tujuanKeys[] = 'kelompok_tani';
        }

        /**
         * 4) SPT query (pakai limit & select kolom penting saja biar cepat)
         */
        $sptQuery = Spt::query()
            ->select([
                'id',
                'nomor_surat',
                'nomor_kwitansi',
                'tahun',
                'bulan',
                'keperluan',
                'tanggal_berangkat',
                'tanggal_kembali',
                'total_biaya',
                'petugas',
                'poktan_nama',
                'tujuan',
                'created_at',
            ])
            ->where(function ($w) use ($q) {
                $w->where('nomor_surat', 'like', "%{$q}%")
                    ->orWhere('nomor_kwitansi', 'like', "%{$q}%")
                    ->orWhere('keperluan', 'like', "%{$q}%");
            });

        if (!empty($tujuanKeys)) {
            $sptQuery->orWhere(function ($w) use ($tujuanKeys) {
                foreach ($tujuanKeys as $key) {
                    $w->orWhere('tujuan', 'like', '%"' . $key . '"%');
                }
            });
        }

        if ($petugas->isNotEmpty()) {
            $sptQuery->orWhere(function ($w) use ($petugas) {
                foreach ($petugas as $p) {
                    if (!empty($p->nama)) $w->orWhere('petugas', 'like', "%{$p->nama}%");
                    if (isset($p->nip) && !empty($p->nip)) $w->orWhere('petugas', 'like', "%{$p->nip}%");
                }
            });
        }

        if ($poktan->isNotEmpty()) {
            $sptQuery->orWhere(function ($w) use ($poktan, $possibleKetuaCols) {
                foreach ($poktan as $pk) {
                    if (!empty($pk->nama_poktan)) {
                        $w->orWhere('poktan_nama', 'like', "%{$pk->nama_poktan}%");
                    }
                    foreach ($possibleKetuaCols as $col) {
                        if (isset($pk->{$col}) && !empty($pk->{$col})) {
                            $w->orWhere('poktan_nama', 'like', "%{$pk->{$col}}%");
                        }
                    }
                }
            });
        }

        $spt = $sptQuery->latest()->limit($limit)->get()->unique('id')->values();

        // Kembalikan JSON untuk dipakai frontend (langsung tampil tabel)
        return response()->json([
            'q' => $q,
            'counts' => [
                'spt' => $spt->count(),
                'petugas' => $petugas->count(),
                'poktan' => $poktan->count(),
            ],
            'data' => [
                'spt' => $spt,
                'petugas' => $petugas,
                'poktan' => $poktan,
            ],
        ]);
    }
}
