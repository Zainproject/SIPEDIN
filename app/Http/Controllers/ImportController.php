<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PetugasImport;
use App\Imports\PoktanImport;
use App\Imports\SptImport;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function importPetugas(Request $request)
    {
        $request->validate([
            'file_petugas' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new PetugasImport, $request->file('file_petugas'));

        return back()->with('success', 'Import Petugas berhasil.');
    }

    public function importPoktan(Request $request)
    {
        $request->validate([
            'file_poktan' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new PoktanImport, $request->file('file_poktan'));

        return back()->with('success', 'Import Poktan berhasil.');
    }

    public function importSpt(Request $request)
    {
        $request->validate([
            'file_spt' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new SptImport, $request->file('file_spt'));

        return back()->with('success', 'Import SPT berhasil.');
    }
}
