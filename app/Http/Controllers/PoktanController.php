<?php

namespace App\Http\Controllers;

use App\Models\Poktan;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PoktanController extends Controller
{
    /* =======================
     * HELPER: SIMPAN AKTIVITAS
     * ======================= */
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
                'redirect_url' => route('poktan.index'),
                'data'         => $data,
            ], JSON_UNESCAPED_UNICODE),
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }

    public function index()
    {
        $jumlahPoktan = Poktan::count();
        $poktan = Poktan::orderBy('created_at', 'desc')->get();

        return view('Datapoktan.Datapoktan', compact('poktan', 'jumlahPoktan'));
    }

    public function create()
    {
        return view('Datapoktan.tambahpoktan');
    }

    /* =======================
     * STORE
     * ======================= */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'nama_poktan' => 'required|string|max:255|unique:poktan,nama_poktan',
                'ketua'       => 'required|string|max:255',
                'desa'        => 'required|string|max:100',
                'kecamatan'   => 'required|string|max:100',
            ], [
                'nama_poktan.unique' => 'Nama Poktan sudah terdaftar di database!',
            ]);

            $poktan = Poktan::create(
                $request->only(['nama_poktan', 'ketua', 'desa', 'kecamatan'])
            );

            // LOG AKTIVITAS (CREATE)
            $this->logActivity($request, 'create', 'Menambah data di Data Poktan', [
                'id'          => $poktan->id,
                'nama_poktan' => $poktan->nama_poktan,
                'ketua'       => $poktan->ketua,
                'desa'        => $poktan->desa,
                'kecamatan'   => $poktan->kecamatan,
            ]);

            return redirect()->route('poktan.index')
                ->with('success', 'Data Poktan berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $poktan = Poktan::findOrFail($id);
        return view('Datapoktan.showpoktan', compact('poktan'));
    }

    public function edit(string $id)
    {
        $poktan = Poktan::findOrFail($id);
        return view('Datapoktan.editpoktan', compact('poktan'));
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update(Request $request, string $id)
    {
        $poktan = Poktan::findOrFail($id);

        try {

            $request->validate([
                'nama_poktan' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('poktan', 'nama_poktan')->ignore($poktan->id),
                ],
                'ketua'     => 'required|string|max:255',
                'desa'      => 'required|string|max:100',
                'kecamatan' => 'required|string|max:100',
            ], [
                'nama_poktan.unique' => 'Nama Poktan sudah digunakan!',
            ]);

            $poktan->update(
                $request->only(['nama_poktan', 'ketua', 'desa', 'kecamatan'])
            );

            // LOG AKTIVITAS (UPDATE)
            $this->logActivity($request, 'update', 'Mengubah data di Data Poktan', [
                'id'          => $poktan->id,
                'nama_poktan' => $poktan->nama_poktan,
                'ketua'       => $poktan->ketua,
                'desa'        => $poktan->desa,
                'kecamatan'   => $poktan->kecamatan,
            ]);

            return redirect()->route('poktan.index')
                ->with('success', 'Data berhasil diupdate');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }
    }

    public function destroy(Request $request, string $id)
    {
        $poktan = Poktan::findOrFail($id);

        $info = [
            'id'          => $poktan->id,
            'nama_poktan' => $poktan->nama_poktan,
            'ketua'       => $poktan->ketua,
            'desa'        => $poktan->desa,
            'kecamatan'   => $poktan->kecamatan,
        ];

        $poktan->delete();

        // LOG AKTIVITAS (DELETE)
        $this->logActivity($request, 'delete', 'Menghapus data di Data Poktan', $info);

        return redirect()->route('poktan.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
