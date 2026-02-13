@extends('index')

@section('main')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Edit Data</h1>

        <form action="{{ route('petugas.update', $petugas) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama"
                    value="{{ old('nama', $petugas->nama) }}" placeholder="Masukkan Nama">

                @error('nama')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nip">NIP</label>
                <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip"
                    value="{{ old('nip', $petugas->nip) }}" placeholder="Masukkan NIP">

                @error('nip')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="pangkat">Pangkat/Gol</label>
                <input type="text" class="form-control @error('pangkat') is-invalid @enderror" id="pangkat"
                    name="pangkat" value="{{ old('pangkat', $petugas->pangkat) }}" placeholder="Masukkan Pangkat/Gol">

                @error('pangkat')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="jabatan">Jabatan</label>
                <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan"
                    name="jabatan" value="{{ old('jabatan', $petugas->jabatan) }}" placeholder="Masukkan Jabatan">

                @error('jabatan')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-warning btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-edit"></i>
                </span>
                <span class="text">Update</span>
            </button>

            <!-- Tombol Kembali -->
            <a href="{{ route('petugas.index') }}" class="btn btn-secondary btn-icon-split ml-2">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </form>

    </div>

    {{-- ========================
     ALERT JS ERROR
======================== --}}
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                alert(@json($errors->first()));
            });
        </script>
    @endif
@endsection
