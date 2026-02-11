<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Spt;
use App\Models\Petugas;

class RekapSuratKeluarController extends Controller
{
    /**
     * Normalisasi value kolom JSON/array/string menjadi array sederhana
     * Support:
     * - array asli
     * - string JSON: '["123","456"]'
     * - string comma-separated: '123,456'
     * - null
     */
    private function normalizeToArray($val): array
    {
        if (is_array($val)) return $val;

        if (is_string($val)) {
            $val = trim($val);
            if ($val === '') return [];

            // coba decode JSON
            $decoded = json_decode($val, true);
            if (is_array($decoded)) return $decoded;

            // fallback comma-separated
            if (str_contains($val, ',')) {
                return array_values(array_filter(array_map('trim', explode(',', $val))));
            }

            // fallback: satu nilai
            return [$val];
        }

        return [];
    }

    private function applyFilter(Request $request, $q)
    {
        if ($request->filled('tahun')) {
            $q->where('tahun', (int) $request->tahun);
        }

        if ($request->filled('bulan')) {
            $q->where('bulan', (int) $request->bulan);
        }

        if ($request->filled('mak')) {
            $q->where('mak', $request->mak);
        }

        // petugas = NIP (kolom petugas json/array)
        if ($request->filled('petugas')) {
            $nip = (string) $request->petugas;

            $q->where(function ($x) use ($nip) {
                // aman: kalau DB support json
                $x->where(function ($j) use ($nip) {
                    // guard: hanya panggil whereJsonContains kalau kolomnya ada
                    // (kalau tidak ada, query builder akan error)
                    $j->whereJsonContains('petugas', $nip);
                })
                    // fallback: string json
                    ->orWhere('petugas', 'like', '%"' . $nip . '"%')
                    // fallback: plain string/comma separated
                    ->orWhere('petugas', 'like', '%' . $nip . '%');
            });
        }

        if ($request->filled('keyword')) {
            $kw = trim((string) $request->keyword);
            $q->where(function ($x) use ($kw) {
                $x->where('nomor_surat', 'like', "%{$kw}%")
                    ->orWhere('nomor_kwitansi', 'like', "%{$kw}%")
                    ->orWhere('keperluan', 'like', "%{$kw}%")
                    ->orWhere('berangkat_dari', 'like', "%{$kw}%")
                    ->orWhere('alat_angkut', 'like', "%{$kw}%");
            });
        }

        return $q;
    }

    public function index(Request $request)
    {
        $q = Spt::query();
        $q = $this->applyFilter($request, $q);

        // ✅ TANPA PAGINATE
        $spts = (clone $q)->orderByDesc('id')->get();

        // KPI
        $totalSurat = (clone $q)->count();
        $totalBiaya = (clone $q)->sum('total_biaya');

        // option filter
        $tahunOptions = Spt::select('tahun')
            ->whereNotNull('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $makOptions = Spt::select('mak')
            ->whereNotNull('mak')
            ->distinct()
            ->orderBy('mak')
            ->pluck('mak');

        // dropdown petugas
        $petugasOptions = Petugas::orderBy('nama')->get(['nip', 'nama']);

        // ====== REKAP PETUGAS (sesuai filter) ======
        $petugasCount = [];
        foreach ((clone $q)->get(['petugas']) as $row) {
            $arr = $this->normalizeToArray($row->petugas);
            foreach ($arr as $nip) {
                $nip = trim((string) $nip);
                if ($nip === '') continue;
                $petugasCount[$nip] = ($petugasCount[$nip] ?? 0) + 1;
            }
        }

        $petugasMap = $petugasOptions->keyBy('nip');

        $rekapPetugas = collect($petugasCount)
            ->map(function ($count, $nip) use ($petugasMap) {
                return [
                    'nip' => $nip,
                    'nama' => $petugasMap[$nip]->nama ?? $nip,
                    'jumlah' => $count,
                ];
            })
            ->sortByDesc('jumlah')
            ->values();

        // ====== REKAP POKTAN (opsional) ======
        $rekapPoktan = collect();

        if (Schema::hasColumn('spts', 'poktan_nama')) {
            $poktanCount = [];
            foreach ((clone $q)->get(['poktan_nama']) as $row) {
                $arr = $this->normalizeToArray($row->poktan_nama);
                foreach ($arr as $nama) {
                    $nama = trim((string) $nama);
                    if ($nama === '') continue;
                    $poktanCount[$nama] = ($poktanCount[$nama] ?? 0) + 1;
                }
            }

            $rekapPoktan = collect($poktanCount)
                ->map(function ($count, $nama) {
                    return [
                        'nama_poktan' => $nama,
                        'jumlah' => $count,
                    ];
                })
                ->sortByDesc('jumlah')
                ->values();
        }

        return view('rekap_surat_keluar.index', compact(
            'spts',
            'totalSurat',
            'totalBiaya',
            'tahunOptions',
            'makOptions',
            'petugasOptions',
            'rekapPetugas',
            'rekapPoktan'
        ));
    }

    public function print(Request $request)
    {
        $q = Spt::query();
        $q = $this->applyFilter($request, $q);

        // print tanpa paginate
        $spts = (clone $q)->orderByDesc('id')->get();

        $totalSurat = (clone $q)->count();
        $totalBiaya = (clone $q)->sum('total_biaya');

        // info filter
        $filterInfo = [
            'tahun' => $request->tahun,
            'bulan' => $request->bulan,
            'mak' => $request->mak,
            'petugas' => $request->petugas,
            'keyword' => $request->keyword,
        ];

        // ✅ jenis cetak: spt|petugas|poktan|all
        $jenis = $request->get('jenis', 'all');

        // rekap petugas untuk print
        $petugasOptions = Petugas::orderBy('nama')->get(['nip', 'nama']);
        $petugasMap = $petugasOptions->keyBy('nip');

        $petugasCount = [];
        foreach ((clone $q)->get(['petugas']) as $row) {
            $arr = $this->normalizeToArray($row->petugas);
            foreach ($arr as $nip) {
                $nip = trim((string) $nip);
                if ($nip === '') continue;
                $petugasCount[$nip] = ($petugasCount[$nip] ?? 0) + 1;
            }
        }

        $rekapPetugas = collect($petugasCount)
            ->map(function ($count, $nip) use ($petugasMap) {
                return [
                    'nip' => $nip,
                    'nama' => $petugasMap[$nip]->nama ?? $nip,
                    'jumlah' => $count,
                ];
            })
            ->sortByDesc('jumlah')
            ->values();

        // rekap poktan untuk print (opsional)
        $rekapPoktan = collect();
        if (Schema::hasColumn('spts', 'poktan_nama')) {
            $poktanCount = [];
            foreach ((clone $q)->get(['poktan_nama']) as $row) {
                $arr = $this->normalizeToArray($row->poktan_nama);
                foreach ($arr as $nama) {
                    $nama = trim((string) $nama);
                    if ($nama === '') continue;
                    $poktanCount[$nama] = ($poktanCount[$nama] ?? 0) + 1;
                }
            }

            $rekapPoktan = collect($poktanCount)
                ->map(function ($count, $nama) {
                    return [
                        'nama_poktan' => $nama,
                        'jumlah' => $count,
                    ];
                })
                ->sortByDesc('jumlah')
                ->values();
        }

        return view('rekap_surat_keluar.print', compact(
            'jenis',
            'spts',
            'rekapPetugas',
            'rekapPoktan',
            'totalSurat',
            'totalBiaya',
            'filterInfo'
        ));
    }
}
