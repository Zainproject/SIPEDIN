@extends('index')

@section('main')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">Import Data</h1>

        @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- IMPORT PETUGAS --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Import Petugas</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('import.petugas') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>File (xlsx/xls/csv)</label>
                        <input type="file" class="form-control" name="file_petugas" required>
                        <small class="text-muted d-block mt-1">Header wajib: <b>nip, nama, pangkat, jabatan</b></small>
                    </div>

                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-upload"></i> Import Petugas
                    </button>
                </form>
            </div>
        </div>

        {{-- IMPORT POKTAN --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Import Poktan</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('import.poktan') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>File (xlsx/xls/csv)</label>
                        <input type="file" class="form-control" name="file_poktan" required>
                        <small class="text-muted d-block mt-1">Header wajib: <b>nama_poktan, ketua, desa,
                                kecamatan</b></small>
                    </div>

                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-upload"></i> Import Poktan
                    </button>
                </form>
            </div>
        </div>

        {{-- IMPORT SPT --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Import Surat SPT</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('import.spt') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>File (xlsx/xls/csv)</label>
                        <input type="file" class="form-control" name="file_spt" required>
                        <small class="text-muted d-block mt-1">
                            Header minimal wajib: <b>nomor_surat</b><br>
                            Kolom multi (petugas/tujuan/arahan/dll) pisahkan pakai <b>;</b> contoh: <b>1212;1214</b>
                        </small>
                    </div>

                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-upload"></i> Import SPT
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
