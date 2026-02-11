@extends('index')

@section('main')
    <div id="content">
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800">Tambah Data Pejabat</h1>
            <p class="mb-4">Form untuk menambahkan data pejabat baru</p>

            <div class="card shadow mb-4">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pejabat.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ old('nama') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip"
                                value="{{ old('nip') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="pangkat">Pangkat/Golongan</label>
                            <input type="text" class="form-control" id="pangkat" name="pangkat"
                                value="{{ old('pangkat') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="jabatan">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan"
                                value="{{ old('jabatan') }}" required>
                        </div>

                        <!-- Tambahan Masa Bakti -->
                        <div class="form-group">
                            <label for="masa_bakti">Masa Bakti</label>
                            <input type="text" class="form-control" id="masa_bakti" name="masa_bakti"
                                placeholder="Contoh: 2024 - 2028" value="{{ old('masa_bakti') }}" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-icon-split">
                            <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                            <span class="text">Simpan</span>
                        </button>

                        <a href="{{ route('pejabat.index') }}" class="btn btn-secondary btn-icon-split ml-2">
                            <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
                            <span class="text">Kembali</span>
                        </a>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
