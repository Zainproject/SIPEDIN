<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Petugas;
use App\Models\Activity;

class PetugasController extends Controller
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
                'redirect_url' => route('petugas.index'),
                'data'         => $data,
            ], JSON_UNESCAPED_UNICODE),
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }

    public function index()
    {
        $petugas = Petugas::orderBy('created_at', 'desc')->get();
        return view('Datapetugas.Datapetugas', compact('petugas'));
    }

    public function create()
    {
        return view('Datapetugas.tambahpetugas');
    }

    /* =======================
     * STORE
     * ======================= */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'nama'    => 'required|string|max:255',
                'nip'     => 'required|string|max:20|unique:petugas,nip',
                'pangkat' => 'required|string|max:100',
                'jabatan' => 'required|string|max:100',
            ], [
                'nip.unique' => 'NIP sudah terdaftar di database!',
            ]);

            $petugas = Petugas::create(
                $request->only(['nama', 'nip', 'pangkat', 'jabatan'])
            );

            // LOG AKTIVITAS (CREATE)
            $this->logActivity($request, 'create', 'Menambah data di Data Petugas', [
                'id'      => $petugas->id,
                'nama'    => $petugas->nama,
                'nip'     => $petugas->nip,
                'pangkat' => $petugas->pangkat,
                'jabatan' => $petugas->jabatan,
            ]);

            return redirect()->route('petugas.index')
                ->with('success', 'Data petugas berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {

            // Kembali ke form dengan error
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }
    }

    /* =======================
     * SHOW
     * ======================= */
    public function show(Petugas $petugas)
    {
        return redirect()->route('petugas.index');
    }

    public function edit(Petugas $petugas)
    {
        return view('Datapetugas.editpetugas', compact('petugas'));
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update(Request $request, Petugas $petugas)
    {
        try {

            $request->validate([
                'nama'    => 'required|string|max:255',
                'nip'     => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('petugas', 'nip')->ignore($petugas->nip, 'nip'),
                ],
                'pangkat' => 'required|string|max:100',
                'jabatan' => 'required|string|max:100',
            ], [
                'nip.unique' => 'NIP sudah digunakan petugas lain!',
            ]);

            $petugas->update($request->only(['nama', 'nip', 'pangkat', 'jabatan']));

            $this->logActivity($request, 'update', 'Mengubah data di Data Petugas', [
                'id'      => $petugas->id ?? null,
                'nama'    => $petugas->nama,
                'nip'     => $petugas->nip,
                'pangkat' => $petugas->pangkat,
                'jabatan' => $petugas->jabatan,
            ]);

            return redirect()->route('petugas.index')
                ->with('success', 'Data berhasil diupdate');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }
    }


    public function destroy(Request $request, Petugas $petugas)
    {
        $info = [
            'id'      => $petugas->id,
            'nama'    => $petugas->nama,
            'nip'     => $petugas->nip,
            'pangkat' => $petugas->pangkat,
            'jabatan' => $petugas->jabatan,
        ];

        $petugas->delete();

        // LOG AKTIVITAS (DELETE)
        $this->logActivity($request, 'delete', 'Menghapus data di Data Petugas', $info);

        return redirect()->route('petugas.index')
            ->with('success', 'Data petugas berhasil dihapus');
    }
}
