@extends('index')

@section('main')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Edit Data</h1>

        <form action="{{ route('poktan.update', $poktan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_poktan">Nama Poktan</label>
                <input type="text" class="form-control @error('nama_poktan') is-invalid @enderror" id="nama_poktan"
                    name="nama_poktan" value="{{ old('nama_poktan', $poktan->nama_poktan) }}"
                    placeholder="Masukkan Nama Poktan">

                @error('nama_poktan')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="ketua">Ketua</label>
                <input type="text" class="form-control @error('ketua') is-invalid @enderror" id="ketua"
                    name="ketua" value="{{ old('ketua', $poktan->ketua) }}" placeholder="Masukkan Nama Ketua">

                @error('ketua')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="desa">Desa</label>
                <input type="text" class="form-control @error('desa') is-invalid @enderror" id="desa" name="desa"
                    value="{{ old('desa', $poktan->desa) }}" placeholder="Masukkan Nama Desa">

                @error('desa')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="kecamatan">Kecamatan</label>
                <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatan"
                    name="kecamatan" value="{{ old('kecamatan', $poktan->kecamatan) }}"
                    placeholder="Masukkan Nama Kecamatan">

                @error('kecamatan')
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
            <a href="{{ route('poktan.index') }}" class="btn btn-secondary btn-icon-split ml-2">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </form>
    </div>

    {{-- ========================
         ALERT JS ERROR
    ========================= --}}
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                alert(@json($errors->first()));
            });
        </script>
    @endif
@endsection
