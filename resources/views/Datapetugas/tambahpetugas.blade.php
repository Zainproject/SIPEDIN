@extends('index')

@section('main')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Tambah Data Petugas</h1>

        <form action="{{ route('petugas.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama">
            </div>

            <div class="form-group">
                <label for="nip">NIP</label>
                <input type="text" class="form-control" id="nip" name="nip" placeholder="Masukkan NIP">
            </div>

            <div class="form-group">
                <label for="pangkat">Pangkat/Gol</label>
                <input type="text" class="form-control" id="pangkat" name="pangkat" placeholder="Masukkan Pangkat/Gol">
            </div>

            <div class="form-group">
                <label for="jabatan">Jabatan</label>
                <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Masukkan Jabatan">
            </div>

            <button type="submit" class="btn btn-success btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Simpan</span>
            </button>

            <a href="{{ route('petugas.index') }}" class="btn btn-secondary btn-icon-split ml-2">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </form>
    </div>
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                alert(@json($errors->first()));
            });
        </script>
    @endif
@endsection
