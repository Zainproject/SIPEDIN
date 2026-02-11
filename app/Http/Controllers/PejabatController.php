<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pejabat;

class PejabatController extends Controller
{
    /**
     * Tampilkan daftar semua pejabat.
     */
    public function index()
    {
        $pejabat = Pejabat::paginate(10);
        return view('Datapejabat.Datapejabat', compact('pejabat'));
    }

    /**
     * Tampilkan form tambah pejabat.
     */
    public function create()
    {
        return view('Datapejabat.tambahpejabat');
    }

    /**
     * Simpan data pejabat baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'nip'        => 'required|string|max:50|unique:pejabats,nip',
            'pangkat'    => 'required|string|max:100',
            'jabatan'    => 'required|string|max:100',
            'masa_bakti' => 'required|string|max:50',
        ]);

        Pejabat::create($validated);

        return redirect()->route('pejabat.index')->with('success', 'Data pejabat berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail pejabat (opsional).
     */
    public function show(string $id)
    {
        $pejabat = Pejabat::findOrFail($id);
        return view('Datapejabat.detailpejabat', compact('pejabat'));
    }

    /**
     * Tampilkan form edit pejabat.
     */
    public function edit(string $id)
    {
        $pejabat = Pejabat::findOrFail($id);
        return view('Datapejabat.editpejabat', compact('pejabat'));
    }

    /**
     * Update data pejabat.
     */
    public function update(Request $request, string $id)
    {
        $pejabat = Pejabat::findOrFail($id);

        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'nip'        => 'required|string|max:50|unique:pejabats,nip,' . $pejabat->id,
            'pangkat'    => 'required|string|max:100',
            'jabatan'    => 'required|string|max:100',
            'masa_bakti' => 'required|string|max:50',
        ]);

        $pejabat->update($validated);

        return redirect()->route('pejabat.index')->with('success', 'Data pejabat berhasil diperbarui.');
    }

    /**
     * Hapus data pejabat.
     */
    public function destroy(string $id)
    {
        $pejabat = Pejabat::findOrFail($id);
        $pejabat->delete();

        return redirect()->route('pejabat.index')->with('success', 'Data pejabat berhasil dihapus.');
    }
}
