<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Spt;
use App\Models\Petugas;
use App\Models\Poktan;
use App\Models\Activity;

use Carbon\Carbon;

class SptController extends Controller
{
    private function logActivity(Request $request, string $action, string $keterangan, array $data = []): void
    {
        Activity::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'method'     => strtoupper($request->method()),
            'route'      => optional($request->route())->getName(),
            'url'        => $request->fullUrl(),
            'payload'    => json_encode([
                'keterangan'   => $keterangan,
                'redirect_url' => route('spt.index'),
                'data'         => $data,
            ], JSON_UNESCAPED_UNICODE),
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }

    private function toArr($v): array
    {
        if (is_array($v)) return array_values($v);
        if (is_null($v)) return [];
        if (is_string($v)) {
            $v = trim($v);
            if ($v === '') return [];
            $decoded = json_decode($v, true);
            return is_array($decoded) ? array_values($decoded) : [$v];
        }
        return array_values((array) $v);
    }

    public function index()
    {
        $spts = Spt::latest()->get();

        $allNip = collect($spts)
            ->flatMap(fn($spt) => $this->toArr($spt->petugas ?? []))
            ->map(fn($x) => trim((string) $x))
            ->filter()
            ->unique()
            ->values();

        $petugasMap = Petugas::whereIn('nip', $allNip)->pluck('nama', 'nip');

        return view('Suratspt.Dataspt', compact('spts', 'petugasMap'));
    }

    public function show($id)
    {
        return redirect()->route('spt.index');
    }

    public function create()
    {
        return view('Suratspt.tambahspt', [
            'petugas' => Petugas::orderBy('nama')->get(),
            'poktan'  => Poktan::orderBy('nama_poktan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'petugas'   => 'required|array|min:1',
            'petugas.*' => 'required',

            'tujuan'   => 'required|array|min:1|max:2',
            'tujuan.*' => 'required',

            'alat_angkut'    => 'required|string',
            'berangkat_dari' => 'required|string',
            'keperluan'      => 'required|string',
            'kehadiran'      => 'required|string',

            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali'   => 'required|date|after_or_equal:tanggal_berangkat',

            'keterangan_biaya'   => 'required|array|min:1',
            'keterangan_biaya.*' => 'required|string',
            'harga_biaya'        => 'required|array|min:1',
            'harga_biaya.*'      => 'required|numeric|min:0',

            'mak' => 'required|string',

            'nomor_surat_awal'    => 'required',
            'nomor_kwitansi_awal' => 'required',

            'arahan'   => 'required|array|min:1',
            'arahan.*' => 'required|string',

            'masalah'   => 'required|array|min:1',
            'masalah.*' => 'required|string',

            'saran'   => 'required|array|min:1',
            'saran.*' => 'required|string',

            'lainnya'   => 'required|array|min:1',
            'lainnya.*' => 'required|string',
        ]);

        // HITUNG
        $start = Carbon::parse($request->tanggal_berangkat);
        $end   = Carbon::parse($request->tanggal_kembali);

        $lamaHari = $start->diffInDays($end) + 1;
        $subtotalPerHari = collect($request->harga_biaya)->sum(fn($v) => (float) $v);
        $totalBiaya = $subtotalPerHari * $lamaHari;

        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $romawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        $spt = Spt::create([
            'petugas' => array_values($request->petugas),
            'tujuan'  => array_values($request->tujuan),

            'poktan_nama'       => $request->poktan_nama ?? [],
            'deskripsi_kota'    => $request->deskripsi_kota ?? [],
            'deskripsi_lainnya' => $request->deskripsi_lainnya ?? [],

            'alat_angkut'    => $request->alat_angkut,
            'berangkat_dari' => $request->berangkat_dari,
            'keperluan'      => $request->keperluan,
            'kehadiran'      => $request->kehadiran,

            'tanggal_berangkat' => $request->tanggal_berangkat,
            'tanggal_kembali'   => $request->tanggal_kembali,

            'keterangan_biaya' => array_values($request->keterangan_biaya),
            'harga_biaya'      => array_values($request->harga_biaya),

            'lama_hari'        => $lamaHari,
            'subtotal_perhari' => $subtotalPerHari,
            'total_biaya'      => $totalBiaya,

            'bulan' => $bulan,
            'tahun' => $tahun,
            'mak'   => $request->mak,

            'nomor_surat' =>
            $request->nomor_surat_awal . '/spt/500.0/' . ($romawi[$bulan] ?? $bulan) . '/' . $tahun,

            'nomor_kwitansi' =>
            $request->nomor_kwitansi_awal . '/spt/500.0/' . ($romawi[$bulan] ?? $bulan) . '/' . $tahun,

            'arahan'  => array_values($request->arahan),
            'masalah' => array_values($request->masalah),
            'saran'   => array_values($request->saran),
            'lainnya' => array_values($request->lainnya),
        ]);

        $this->logActivity($request, 'create', 'Menambah data di Data SPT', [
            'id' => $spt->id,
            'nomor_surat' => $spt->nomor_surat,
            'nomor_kwitansi' => $spt->nomor_kwitansi,
        ]);

        return redirect()->route('spt.index')->with('success', 'SPT berhasil dibuat');
    }

    public function edit($id)
    {
        $spt = Spt::findOrFail($id);

        $nomorSuratAwal    = explode('/', (string) $spt->nomor_surat)[0] ?? '';
        $nomorKwitansiAwal = explode('/', (string) $spt->nomor_kwitansi)[0] ?? '';

        return view('Suratspt.editspt', [
            'spt' => $spt,
            'petugas' => Petugas::orderBy('nama')->get(),
            'poktan'  => Poktan::orderBy('nama_poktan')->get(),
            'nomor_surat_awal'    => $nomorSuratAwal,
            'nomor_kwitansi_awal' => $nomorKwitansiAwal,
        ]);
    }

    // ✅ INI YANG WAJIB ADA (biar gak error)
    public function update(Request $request, $id)
    {
        $spt = Spt::findOrFail($id);

        $request->validate([
            'petugas'   => 'required|array|min:1',
            'petugas.*' => 'required',

            'tujuan'   => 'required|array|min:1|max:2',
            'tujuan.*' => 'required',

            'alat_angkut'    => 'required|string',
            'berangkat_dari' => 'required|string',
            'keperluan'      => 'required|string',
            'kehadiran'      => 'required|string',

            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali'   => 'required|date|after_or_equal:tanggal_berangkat',

            'keterangan_biaya'   => 'required|array|min:1',
            'keterangan_biaya.*' => 'required|string',
            'harga_biaya'        => 'required|array|min:1',
            'harga_biaya.*'      => 'required|numeric|min:0',

            'mak' => 'required|string',

            'nomor_surat_awal'    => 'required',
            'nomor_kwitansi_awal' => 'required',

            'arahan'   => 'required|array|min:1',
            'arahan.*' => 'required|string',

            'masalah'   => 'required|array|min:1',
            'masalah.*' => 'required|string',

            'saran'   => 'required|array|min:1',
            'saran.*' => 'required|string',

            'lainnya'   => 'required|array|min:1',
            'lainnya.*' => 'required|string',
        ]);

        // HITUNG
        $start = Carbon::parse($request->tanggal_berangkat);
        $end   = Carbon::parse($request->tanggal_kembali);

        $lamaHari = $start->diffInDays($end) + 1;
        $subtotalPerHari = collect($request->harga_biaya)->sum(fn($v) => (float) $v);
        $totalBiaya = $subtotalPerHari * $lamaHari;

        $bulan = (int) ($request->bulan ?? ($spt->bulan ?? now()->month));
        $tahun = (int) ($request->tahun ?? ($spt->tahun ?? now()->year));

        $romawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        $spt->update([
            'petugas' => array_values($request->petugas),
            'tujuan'  => array_values($request->tujuan),

            'poktan_nama'       => $request->poktan_nama ?? [],
            'deskripsi_kota'    => $request->deskripsi_kota ?? [],
            'deskripsi_lainnya' => $request->deskripsi_lainnya ?? [],

            'alat_angkut'    => $request->alat_angkut,
            'berangkat_dari' => $request->berangkat_dari,
            'keperluan'      => $request->keperluan,
            'kehadiran'      => $request->kehadiran,

            'tanggal_berangkat' => $request->tanggal_berangkat,
            'tanggal_kembali'   => $request->tanggal_kembali,

            'keterangan_biaya' => array_values($request->keterangan_biaya),
            'harga_biaya'      => array_values($request->harga_biaya),

            'lama_hari'        => $lamaHari,
            'subtotal_perhari' => $subtotalPerHari,
            'total_biaya'      => $totalBiaya,

            'bulan' => $bulan,
            'tahun' => $tahun,
            'mak'   => $request->mak,

            'nomor_surat' =>
            $request->nomor_surat_awal . '/spt/500.0/' . ($romawi[$bulan] ?? $bulan) . '/' . $tahun,

            'nomor_kwitansi' =>
            $request->nomor_kwitansi_awal . '/spt/500.0/' . ($romawi[$bulan] ?? $bulan) . '/' . $tahun,

            'arahan'  => array_values($request->arahan),
            'masalah' => array_values($request->masalah),
            'saran'   => array_values($request->saran),
            'lainnya' => array_values($request->lainnya),
        ]);

        $this->logActivity($request, 'update', 'Mengubah data di Data SPT', [
            'id' => $spt->id,
            'nomor_surat' => $spt->nomor_surat,
            'nomor_kwitansi' => $spt->nomor_kwitansi,
        ]);

        return redirect()->route('spt.index')->with('success', 'Data SPT berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $spt = Spt::findOrFail($id);
        $spt->delete();

        $this->logActivity($request, 'delete', 'Menghapus data di Data SPT', [
            'id' => $spt->id,
            'nomor_surat' => $spt->nomor_surat,
            'nomor_kwitansi' => $spt->nomor_kwitansi,
        ]);

        return redirect()->route('spt.index')->with('success', 'Data SPT berhasil dihapus');
    }

    public function print($id = null)
    {
        $user = Auth::user();

        if ($id) {
            return view('spj.cetak', [
                'mode' => 'single',
                'spt'  => Spt::findOrFail($id),
                'user' => $user,
            ]);
        }

        return view('spj.cetak', [
            'mode' => 'all',
            'spts' => Spt::orderByDesc('created_at')->get(),
            'user' => $user,
        ]);
    }
}
