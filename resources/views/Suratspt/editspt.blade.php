@extends('index')

@section('main')
    @php
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        // pakai bulan/tahun dari data SPT (biar info format nomor tetap konsisten saat edit)
        $bulan = (int) ($spt->bulan ?? now()->month);
        $bulanRomawi = $romawi[$bulan] ?? $bulan;
        $tahun = (int) ($spt->tahun ?? now()->year);

        // helper decode JSON/array aman
        $toArray = function ($v) {
            if (is_array($v)) {
                return array_values($v);
            }
            if (is_null($v)) {
                return [];
            }
            if (is_string($v)) {
                $v = trim($v);
                if ($v === '') {
                    return [];
                }
                $d = json_decode($v, true);
                return is_array($d) ? array_values($d) : [$v];
            }
            return array_values((array) $v);
        };

        $petugasArr = $toArray($spt->petugas);
        $tujuanArr = $toArray($spt->tujuan);

        $poktanNamaArr = $toArray($spt->poktan_nama);
        $desKotaArr = $toArray($spt->deskripsi_kota);
        $desLainArr = $toArray($spt->deskripsi_lainnya);

        $ketBiayaArr = $toArray($spt->keterangan_biaya);
        $hargaBiayaArr = $toArray($spt->harga_biaya);

        $arahanArr = $toArray($spt->arahan);
        $masalahArr = $toArray($spt->masalah);
        $saranArr = $toArray($spt->saran);
        $lainnyaArr = $toArray($spt->lainnya);

        // fallback minimal 1 item supaya UI tidak kosong
        if (count($petugasArr) < 1) {
            $petugasArr = [''];
        }
        if (count($tujuanArr) < 1) {
            $tujuanArr = [''];
        }
        if (count($ketBiayaArr) < 1) {
            $ketBiayaArr = [''];
        }
        if (count($hargaBiayaArr) < 1) {
            $hargaBiayaArr = [0];
        }
        if (count($arahanArr) < 1) {
            $arahanArr = [''];
        }
        if (count($masalahArr) < 1) {
            $masalahArr = [''];
        }
        if (count($saranArr) < 1) {
            $saranArr = [''];
        }
        if (count($lainnyaArr) < 1) {
            $lainnyaArr = [''];
        }
    @endphp

    <div id="content">
        <div class="container-fluid">

            <div class="d-sm-flex align-items-center justify-content-between mb-3">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Edit Data SPT</h1>
                    <small class="text-muted">Perbarui data SPT. Pastikan setiap step terisi lengkap.</small>
                </div>
                <a href="{{ route('spt.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">

                    <form id="formSPT" action="{{ route('spt.update', $spt->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- STEP 1 -->
                        <div class="step" id="step1">
                            <h4>Step 1: Petugas & Tujuan</h4>

                            <!-- Petugas -->
                            <div id="petugasContainer">
                                <label>Petugas</label>

                                @foreach (old('petugas', $petugasArr) as $i => $nipVal)
                                    <div class="input-group mb-2">
                                        <select name="petugas[]" class="form-control" required>
                                            <option value="">-- Pilih Petugas --</option>
                                            @foreach ($petugas as $p)
                                                <option value="{{ $p->nip }}"
                                                    {{ (string) $nipVal === (string) $p->nip ? 'selected' : '' }}>
                                                    {{ $p->nama }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <div class="input-group-append">
                                            @if ($i === 0)
                                                <button type="button" class="btn btn-success addPetugas">+</button>
                                            @else
                                                <button type="button" class="btn btn-danger removePetugas">−</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Tujuan --}}
                            <div id="tujuanContainer" class="mt-2">
                                <label>Tujuan</label>

                                @foreach (old('tujuan', $tujuanArr) as $i => $tjVal)
                                    @php
                                        // ambil detail tujuan sesuai index (aman walau array detail tidak sama panjang)
                                        $poktanVal = old('poktan_nama.' . $i, $poktanNamaArr[$i] ?? '');
                                        $kotaVal = old('deskripsi_kota.' . $i, $desKotaArr[$i] ?? '');
                                        $lainVal = old('deskripsi_lainnya.' . $i, $desLainArr[$i] ?? '');
                                    @endphp

                                    <div class="tujuan-item mb-2">
                                        <div class="input-group">
                                            <select name="tujuan[]" class="form-control tujuan-select" required>
                                                <option value="">-- Pilih Tujuan --</option>
                                                <option value="kelompok_tani"
                                                    {{ $tjVal === 'kelompok_tani' ? 'selected' : '' }}>
                                                    Kelompok Tani
                                                </option>
                                                <option value="kabupaten_kota"
                                                    {{ $tjVal === 'kabupaten_kota' ? 'selected' : '' }}>
                                                    Kabupaten/Kota
                                                </option>
                                                <option value="lainnya" {{ $tjVal === 'lainnya' ? 'selected' : '' }}>
                                                    Lainnya
                                                </option>
                                            </select>

                                            <div class="input-group-append">
                                                @if ($i === 0)
                                                    <button type="button" class="btn btn-success addTujuan">+</button>
                                                @else
                                                    <button type="button" class="btn btn-danger removeTujuan">−</button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="kelompok-tani-field d-none mt-2">
                                            <select name="poktan_nama[]" class="form-control">
                                                <option value="">-- Pilih Poktan --</option>
                                                @foreach ($poktan as $pt)
                                                    <option value="{{ $pt->nama_poktan }}"
                                                        {{ $poktanVal === $pt->nama_poktan ? 'selected' : '' }}>
                                                        {{ $pt->nama_poktan }} ({{ $pt->desa }}/{{ $pt->kecamatan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="kabupaten-kota-field d-none mt-2">
                                            <textarea name="deskripsi_kota[]" class="form-control" placeholder="Deskripsi kota...">{{ $kotaVal }}</textarea>
                                        </div>

                                        <div class="lainnya-field d-none mt-2">
                                            <textarea name="deskripsi_lainnya[]" class="form-control" placeholder="Deskripsi lainnya...">{{ $lainVal }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group">
                                <label for="alat_angkut">Alat Angkut</label>
                                <input type="text" name="alat_angkut" id="alat_angkut" class="form-control"
                                    value="{{ old('alat_angkut', $spt->alat_angkut) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="berangkat_dari">Berangkat dari</label>
                                <input type="text" name="berangkat_dari" id="berangkat_dari" class="form-control"
                                    value="{{ old('berangkat_dari', $spt->berangkat_dari) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="keperluan">Keperluan</label>
                                <input type="text" name="keperluan" class="form-control"
                                    value="{{ old('keperluan', $spt->keperluan) }}" required>

                                <small class="form-text text-muted">
                                    Jika tujuan lebih dari satu (misalnya Kelompok Tani dan Kabupaten/Kota),
                                    pisahkan keperluan dengan tanda <b>;</b>.
                                    <br>
                                    Contoh: <i>Observasi Lapangan; Kunjungan belajar</i>
                                    <br>
                                    (Bagian pertama untuk tujuan pertama, bagian kedua untuk tujuan berikutnya).
                                </small>
                            </div>


                            <button type="button" class="btn btn-primary next-step">Lanjut</button>
                        </div>

                        <!-- STEP 2 -->
                        <div class="step d-none" id="step2">
                            <h4>Step 2: Tanggal & Biaya</h4>

                            <div class="form-group">
                                <label for="tanggal_berangkat">Tanggal Berangkat</label>
                                <input type="date" id="tanggal_berangkat" name="tanggal_berangkat" class="form-control"
                                    value="{{ old('tanggal_berangkat', $spt->tanggal_berangkat) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="tanggal_kembali">Tanggal Kembali</label>
                                <input type="date" id="tanggal_kembali" name="tanggal_kembali" class="form-control"
                                    value="{{ old('tanggal_kembali', $spt->tanggal_kembali) }}" required>
                            </div>

                            <label>Biaya (Keterangan & Harga / Hari)</label>
                            <div id="biayaContainer">
                                @php
                                    $ketOldArr = old('keterangan_biaya', $ketBiayaArr);
                                    $hargaOldArr = old('harga_biaya', $hargaBiayaArr);
                                    $rowsBiaya = max(count($ketOldArr), count($hargaOldArr));
                                    $rowsBiaya = max($rowsBiaya, 1);
                                @endphp

                                @for ($i = 0; $i < $rowsBiaya; $i++)
                                    @php
                                        $ket = $ketOldArr[$i] ?? '';
                                        $harga = $hargaOldArr[$i] ?? 0;
                                    @endphp
                                    <div class="input-group mb-2 biaya-item">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text nomor-biaya">{{ $i + 1 }}</span>
                                        </div>

                                        <input type="text" name="keterangan_biaya[]" class="form-control"
                                            placeholder="Keterangan (contoh: Uang harian)" value="{{ $ket }}"
                                            required>

                                        <input type="number" name="harga_biaya[]" class="form-control harga-biaya"
                                            placeholder="Harga / hari (contoh: 150000)" value="{{ $harga }}"
                                            required min="0" step="1">

                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success addBiaya">+</button>
                                            <button type="button" class="btn btn-danger removeBiaya">−</button>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="form-group mt-2">
                                <label>Lama Hari</label>
                                <input type="text" id="lama_hari" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label>Subtotal Biaya (Jumlah semua item / hari)</label>
                                <input type="text" id="subtotal_biaya" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label>Total Biaya (Subtotal × Lama Hari)</label>
                                <input type="text" id="total_biaya" class="form-control" readonly>
                            </div>

                            <button type="button" class="btn btn-secondary prev-step">Kembali</button>
                            <button type="button" class="btn btn-primary next-step">Lanjut</button>
                        </div>

                        <!-- STEP 3 -->
                        <div class="step d-none" id="step3">
                            <h4>Step 3: Kehadiran di Lapangan & Nomor Surat</h4>

                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">

                            <div class="form-group">
                                <label>Yang Hadir</label>
                                <input type="text" name="kehadiran" class="form-control"
                                    value="{{ old('kehadiran', $spt->kehadiran) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="mak">MAK</label>
                                <select name="mak" class="form-control" required>
                                    <option value="">-- Pilih MAK --</option>
                                    <option value="001" {{ old('mak', $spt->mak) == '001' ? 'selected' : '' }}>001 -
                                        Operasional</option>
                                    <option value="002" {{ old('mak', $spt->mak) == '002' ? 'selected' : '' }}>002 -
                                        Perjalanan Dinas</option>
                                    <option value="003" {{ old('mak', $spt->mak) == '003' ? 'selected' : '' }}>003 -
                                        Kegiatan Lapangan</option>
                                    <option value="004" {{ old('mak', $spt->mak) == '004' ? 'selected' : '' }}>004 -
                                        Administrasi</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="nomor_surat">Nomor Surat</label>
                                <input type="text" name="nomor_surat_awal" class="form-control"
                                    value="{{ old('nomor_surat_awal', $nomor_surat_awal ?? '') }}"
                                    placeholder="Isi nomor saja, contoh: 102" required>
                                <small class="form-text text-muted">
                                    Format akhir: [Nomor]/spt/500.0/{{ $bulanRomawi }}/{{ $tahun }}
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="nomor_kwitansi">Nomor Kwitansi</label>
                                <input type="text" name="nomor_kwitansi_awal" class="form-control"
                                    value="{{ old('nomor_kwitansi_awal', $nomor_kwitansi_awal ?? '') }}"
                                    placeholder="Isi nomor saja, contoh: 102" required>
                                <small class="form-text text-muted">
                                    Format akhir: [Nomor]/spt/500.0/{{ $bulanRomawi }}/{{ $tahun }}
                                </small>
                            </div>

                            <button type="button" class="btn btn-secondary prev-step">Kembali</button>
                            <button type="button" class="btn btn-primary next-step">Lanjut</button>
                        </div>

                        <!-- STEP 4 -->
                        <div class="step d-none" id="step4">
                            <h4>Step 4: Petunjuk / Arahan</h4>

                            <label>Arahan</label>
                            <div id="arahanContainer">
                                @foreach (old('arahan', $arahanArr) as $i => $val)
                                    <div class="input-group mb-2 arahan-item">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text nomor-arahan">{{ $i + 1 }}</span>
                                        </div>
                                        <input type="text" name="arahan[]" class="form-control"
                                            value="{{ $val }}" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success addArahan">+</button>
                                            <button type="button" class="btn btn-danger removeArahan">−</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label>Masalah / Temuan</label>
                            <div id="masalahContainer">
                                @foreach (old('masalah', $masalahArr) as $i => $val)
                                    <div class="input-group mb-2 masalah-item">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text nomor-masalah">{{ $i + 1 }}</span>
                                        </div>
                                        <input type="text" name="masalah[]" class="form-control"
                                            value="{{ $val }}" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success addMasalah">+</button>
                                            <button type="button" class="btn btn-danger removeMasalah">−</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label>Saran Tindakan</label>
                            <div id="saranContainer">
                                @foreach (old('saran', $saranArr) as $i => $val)
                                    <div class="input-group mb-2 saran-item">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text nomor-saran">{{ $i + 1 }}</span>
                                        </div>
                                        <input type="text" name="saran[]" class="form-control"
                                            value="{{ $val }}" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success addSaran">+</button>
                                            <button type="button" class="btn btn-danger removeSaran">−</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label>Lain-lain</label>
                            <div id="lainnyaContainer">
                                @foreach (old('lainnya', $lainnyaArr) as $i => $val)
                                    <div class="input-group mb-2 lainnya-item">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text nomor-lainnya">{{ $i + 1 }}</span>
                                        </div>
                                        <input type="text" name="lainnya[]" class="form-control"
                                            value="{{ $val }}" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success addLainnya">+</button>
                                            <button type="button" class="btn btn-danger removeLainnya">−</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-secondary prev-step">Kembali</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Template Petugas -->
    <template id="petugasTemplate">
        <div class="input-group mb-2">
            <select name="petugas[]" class="form-control" required>
                <option value="">-- Pilih Petugas --</option>
                @foreach ($petugas as $p)
                    <option value="{{ $p->nip }}">{{ $p->nama }}</option>
                @endforeach
            </select>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger removePetugas">−</button>
            </div>
        </div>
    </template>

    <!-- Template Tujuan -->
    <template id="tujuanTemplate">
        <div class="tujuan-item mb-2">
            <div class="input-group">
                <select name="tujuan[]" class="form-control tujuan-select" required>
                    <option value="">-- Pilih Tujuan --</option>
                    <option value="kelompok_tani">Kelompok Tani</option>
                    <option value="kabupaten_kota">Kabupaten/Kota</option>
                    <option value="lainnya">Lainnya</option>
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger removeTujuan">−</button>
                </div>
            </div>

            <div class="kelompok-tani-field d-none mt-2">
                <select name="poktan_nama[]" class="form-control">
                    <option value="">-- Pilih Poktan --</option>
                    @foreach ($poktan as $pt)
                        <option value="{{ $pt->nama_poktan }}">
                            {{ $pt->nama_poktan }} ({{ $pt->desa }}/{{ $pt->kecamatan }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="kabupaten-kota-field d-none mt-2">
                <textarea name="deskripsi_kota[]" class="form-control" placeholder="Deskripsi kota..."></textarea>
            </div>

            <div class="lainnya-field d-none mt-2">
                <textarea name="deskripsi_lainnya[]" class="form-control" placeholder="Deskripsi lainnya..."></textarea>
            </div>
        </div>
    </template>

    <!-- Template Biaya -->
    <template id="biayaTemplate">
        <div class="input-group mb-2 biaya-item">
            <div class="input-group-prepend">
                <span class="input-group-text nomor-biaya"></span>
            </div>

            <input type="text" name="keterangan_biaya[]" class="form-control"
                placeholder="Keterangan (contoh: Uang harian)" required>

            <input type="number" name="harga_biaya[]" class="form-control harga-biaya"
                placeholder="Harga / hari (contoh: 150000)" required min="0" step="1">

            <div class="input-group-append">
                <button type="button" class="btn btn-success addBiaya">+</button>
                <button type="button" class="btn btn-danger removeBiaya">−</button>
            </div>
        </div>
    </template>

    <script>
        // ALERT ERROR
        document.addEventListener("DOMContentLoaded", function() {
            @if ($errors->any())
                alert(@json(implode("\n", $errors->all())));
            @endif
        });

        function refreshNumber(selector) {
            document.querySelectorAll(selector).forEach((el, i) => el.textContent = i + 1);
        }

        function formatRupiah(n) {
            const v = Math.round(n || 0);
            return "Rp " + v.toLocaleString('id-ID');
        }

        function getLamaHari() {
            const tglBerangkat = document.getElementById("tanggal_berangkat");
            const tglKembali = document.getElementById("tanggal_kembali");

            if (!tglBerangkat?.value || !tglKembali?.value) return 0;

            const start = new Date(tglBerangkat.value);
            const end = new Date(tglKembali.value);

            const diff = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
            return diff > 0 ? diff : -1;
        }

        function getSubtotalPerHari() {
            let subtotal = 0;
            document.querySelectorAll(".harga-biaya").forEach(inp => {
                const v = parseFloat(inp.value || "0");
                if (!isNaN(v)) subtotal += v;
            });
            return subtotal;
        }

        function updateBiayaSummary() {
            const lamaEl = document.getElementById("lama_hari");
            const subtotalEl = document.getElementById("subtotal_biaya");
            const totalEl = document.getElementById("total_biaya");

            const lama = getLamaHari();
            const subtotal = getSubtotalPerHari();

            if (!lamaEl || !subtotalEl || !totalEl) return;

            if (lama === -1) {
                lamaEl.value = "Tanggal tidak valid";
                subtotalEl.value = formatRupiah(subtotal);
                totalEl.value = "Tanggal tidak valid";
                return;
            }

            if (lama === 0) {
                lamaEl.value = "";
                subtotalEl.value = formatRupiah(subtotal);
                totalEl.value = "";
                return;
            }

            lamaEl.value = lama + " hari";
            subtotalEl.value = formatRupiah(subtotal);
            totalEl.value = formatRupiah(subtotal * lama);
        }

        // CLICK HANDLER (ADD/REMOVE)
        document.addEventListener("click", function(e) {

            // PETUGAS
            if (e.target.classList.contains("addPetugas")) {
                const template = document.getElementById("petugasTemplate");
                document.getElementById("petugasContainer")?.appendChild(template.content.cloneNode(true));
            }
            if (e.target.classList.contains("removePetugas")) {
                e.target.closest(".input-group")?.remove();
            }

            // TUJUAN (max 2)
            if (e.target.classList.contains("addTujuan")) {
                const container = document.getElementById("tujuanContainer");
                const count = container?.querySelectorAll(".tujuan-item").length || 0;
                if (count >= 2) return alert("Maksimal 2 tujuan saja");

                const template = document.getElementById("tujuanTemplate");
                container?.appendChild(template.content.cloneNode(true));

                // setelah nambah, trigger show/hide sesuai pilihan default
                setTimeout(() => {
                    container.querySelectorAll(".tujuan-select").forEach(sel => {
                        sel.dispatchEvent(new Event('change'));
                    });
                }, 0);
            }
            if (e.target.classList.contains("removeTujuan")) {
                const items = document.querySelectorAll(".tujuan-item");
                if (items.length <= 1) return alert("Minimal 1 tujuan wajib ada");
                e.target.closest(".tujuan-item")?.remove();
            }

            // BIAYA
            if (e.target.classList.contains("addBiaya")) {
                const template = document.getElementById("biayaTemplate");
                document.getElementById("biayaContainer")?.appendChild(template.content.cloneNode(true));
                refreshNumber(".nomor-biaya");
                updateBiayaSummary();
            }

            if (e.target.classList.contains("removeBiaya")) {
                const items = document.querySelectorAll(".biaya-item");
                if (items.length <= 1) return alert("Minimal 1 biaya wajib ada");
                e.target.closest(".biaya-item")?.remove();
                refreshNumber(".nomor-biaya");
                updateBiayaSummary();
            }

            // ARAHAN
            if (e.target.classList.contains("addArahan")) {
                const html = `
                <div class="input-group mb-2 arahan-item">
                    <div class="input-group-prepend">
                        <span class="input-group-text nomor-arahan"></span>
                    </div>
                    <input type="text" name="arahan[]" class="form-control" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success addArahan">+</button>
                        <button type="button" class="btn btn-danger removeArahan">−</button>
                    </div>
                </div>`;
                document.getElementById("arahanContainer")?.insertAdjacentHTML("beforeend", html);
                refreshNumber(".nomor-arahan");
            }
            if (e.target.classList.contains("removeArahan")) {
                const items = document.querySelectorAll(".arahan-item");
                if (items.length <= 1) return alert("Minimal 1 arahan wajib ada");
                e.target.closest(".arahan-item")?.remove();
                refreshNumber(".nomor-arahan");
            }

            // MASALAH
            if (e.target.classList.contains("addMasalah")) {
                const html = `
                <div class="input-group mb-2 masalah-item">
                    <div class="input-group-prepend">
                        <span class="input-group-text nomor-masalah"></span>
                    </div>
                    <input type="text" name="masalah[]" class="form-control" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success addMasalah">+</button>
                        <button type="button" class="btn btn-danger removeMasalah">−</button>
                    </div>
                </div>`;
                document.getElementById("masalahContainer")?.insertAdjacentHTML("beforeend", html);
                refreshNumber(".nomor-masalah");
            }
            if (e.target.classList.contains("removeMasalah")) {
                const items = document.querySelectorAll(".masalah-item");
                if (items.length <= 1) return alert("Minimal 1 masalah wajib ada");
                e.target.closest(".masalah-item")?.remove();
                refreshNumber(".nomor-masalah");
            }

            // SARAN
            if (e.target.classList.contains("addSaran")) {
                const html = `
                <div class="input-group mb-2 saran-item">
                    <div class="input-group-prepend">
                        <span class="input-group-text nomor-saran"></span>
                    </div>
                    <input type="text" name="saran[]" class="form-control" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success addSaran">+</button>
                        <button type="button" class="btn btn-danger removeSaran">−</button>
                    </div>
                </div>`;
                document.getElementById("saranContainer")?.insertAdjacentHTML("beforeend", html);
                refreshNumber(".nomor-saran");
            }
            if (e.target.classList.contains("removeSaran")) {
                const items = document.querySelectorAll(".saran-item");
                if (items.length <= 1) return alert("Minimal 1 saran wajib ada");
                e.target.closest(".saran-item")?.remove();
                refreshNumber(".nomor-saran");
            }

            // LAINNYA
            if (e.target.classList.contains("addLainnya")) {
                const html = `
                <div class="input-group mb-2 lainnya-item">
                    <div class="input-group-prepend">
                        <span class="input-group-text nomor-lainnya"></span>
                    </div>
                    <input type="text" name="lainnya[]" class="form-control" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success addLainnya">+</button>
                        <button type="button" class="btn btn-danger removeLainnya">−</button>
                    </div>
                </div>`;
                document.getElementById("lainnyaContainer")?.insertAdjacentHTML("beforeend", html);
                refreshNumber(".nomor-lainnya");
            }
            if (e.target.classList.contains("removeLainnya")) {
                const items = document.querySelectorAll(".lainnya-item");
                if (items.length <= 1) return alert("Minimal 1 lainnya wajib ada");
                e.target.closest(".lainnya-item")?.remove();
                refreshNumber(".nomor-lainnya");
            }
        });

        // CHANGE HANDLER: TUJUAN SELECT + TANGGAL
        document.addEventListener("change", function(e) {
            if (e.target.classList.contains("tujuan-select")) {
                const item = e.target.closest(".tujuan-item");
                if (!item) return;

                const kt = item.querySelector(".kelompok-tani-field");
                const kk = item.querySelector(".kabupaten-kota-field");
                const ln = item.querySelector(".lainnya-field");

                kt?.classList.add("d-none");
                kk?.classList.add("d-none");
                ln?.classList.add("d-none");

                if (e.target.value === "kelompok_tani") kt?.classList.remove("d-none");
                if (e.target.value === "kabupaten_kota") kk?.classList.remove("d-none");
                if (e.target.value === "lainnya") ln?.classList.remove("d-none");
            }

            if (e.target.id === "tanggal_berangkat" || e.target.id === "tanggal_kembali") {
                updateBiayaSummary();
            }
        });

        // INPUT HANDLER: BIAYA HARGA
        document.addEventListener("input", function(e) {
            if (e.target.classList.contains("harga-biaya")) updateBiayaSummary();
        });

        // STEP NAV + VALIDASI
        document.addEventListener("DOMContentLoaded", function() {
            let currentStep = 0;
            const steps = document.querySelectorAll(".step");

            function showStep(i) {
                steps.forEach((step, idx) => step.classList.toggle("d-none", idx !== i));
            }

            function validateStep(stepIndex) {
                const step = steps[stepIndex];
                const requiredFields = step.querySelectorAll("[required]");
                let invalid = [];

                requiredFields.forEach(field => {
                    const val = (field.value || "").trim();
                    if (val === "") {
                        invalid.push(field);
                        field.classList.add("is-invalid");
                    } else {
                        field.classList.remove("is-invalid");
                    }
                });

                if (invalid.length > 0) {
                    alert("Masih ada kolom wajib yang belum diisi pada step ini!");
                    invalid[0].focus();
                    return false;
                }

                if (step.id === "step2") {
                    const lama = getLamaHari();
                    if (lama === -1) {
                        alert("Tanggal kembali harus sama atau setelah tanggal berangkat!");
                        return false;
                    }
                }

                return true;
            }

            document.querySelectorAll(".next-step").forEach(btn => {
                btn.addEventListener("click", e => {
                    e.preventDefault();
                    if (!validateStep(currentStep)) return;
                    if (currentStep < steps.length - 1) currentStep++;
                    showStep(currentStep);
                });
            });

            document.querySelectorAll(".prev-step").forEach(btn => {
                btn.addEventListener("click", e => {
                    e.preventDefault();
                    if (currentStep > 0) currentStep--;
                    showStep(currentStep);
                });
            });

            showStep(currentStep);
            refreshNumber(".nomor-biaya");
            updateBiayaSummary();

            // penting: saat edit, tampilkan field tujuan sesuai value awal
            document.querySelectorAll(".tujuan-select").forEach(sel => {
                sel.dispatchEvent(new Event('change'));
            });

            refreshNumber(".nomor-arahan");
            refreshNumber(".nomor-masalah");
            refreshNumber(".nomor-saran");
            refreshNumber(".nomor-lainnya");
        });
    </script>
@endsection
