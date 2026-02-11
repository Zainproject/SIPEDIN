@extends('index')

@section('main')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Tambah Data Poktan</h1>

        <form action="{{ route('poktan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama">Nama Poktan</label>
                <input type="text" class="form-control" id="nama_poktan" name="nama_poktan"
                    placeholder="Masukkan Nama Poktan">
            </div>

            <div class="form-group">
                <label for="nip">Nama Ketua</label>
                <input type="text" class="form-control" id="ketua" name="ketua" placeholder="Masukkan Nama Ketua">
            </div>

            <div class="form-group">
                <label for="pangkat">Desa</label>
                <input type="text" class="form-control" id="desa" name="desa" placeholder="Masukkan Nama Desa">
            </div>

            <div class="form-group">
                <label for="jabatan">Kecamatan</label>
                <input type="text" class="form-control" id="kecamatan" name="kecamatan"
                    placeholder="Masukkan Nama Kecamatan">
            </div>

            <button type="submit" class="btn btn-success btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Simpan</span>
            </button>

            <a href="{{ route('poktan.index') }}" class="btn btn-secondary btn-icon-split ml-2">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </form>
    </div>
@endsection
