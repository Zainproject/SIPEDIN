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

        $bulan = (int) now()->format('n');
        $bulanRomawi = $romawi[$bulan] ?? $bulan;
        $tahun = now()->format('Y');
    @endphp

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0 text-gray-800">Tambah Data SPT</h1>

                <a href="{{ route('spt.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">

                    <form id="formSPT" action="{{ route('spt.store') }}" method="POST">
                        @csrf

                        <!-- STEP 1 -->
                        <div class="step" id="step1">
                            <h4>Step 1: Petugas & Tujuan</h4>

                            <!-- Petugas -->
                            <div id="petugasContainer">
                                <label>Petugas</label>
                                <div class="input-group mb-2">
                                    <select name="petugas[]" class="form-control" required>
                                        <option value="">-- Pilih Petugas --</option>
                                        @foreach ($petugas as $p)
                                            <option value="{{ $p->nip }}"
                                                {{ collect(old('petugas'))->contains($p->nip) ? 'selected' : '' }}>
                                                {{ $p->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success addPetugas">+</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Tujuan --}}
                            <div id="tujuanContainer">
                                <label>Tujuan</label>

                                <div class="tujuan-item mb-2">
                                    <div class="input-group">
                                        <select name="tujuan[]" class="form-control tujuan-select" required>
                                            <option value="">-- Pilih Tujuan --</option>
                                            <option value="kelompok_tani">Kelompok Tani</option>
                                            <option value="kabupaten_kota">Kabupaten/Kota</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success addTujuan">+</button>
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
                            </div>

                            <div class="form-group">
                                <label for="alat_angkut">Alat Angkut</label>
                                <input type="text" name="alat_angkut" id="alat_angkut" class="form-control"
                                    value="{{ old('alat_angkut', optional($spt ?? null)->alat_angkut ?? '') }}" required>
                            </div>

                            <!-- Berangkat dari -->
                            <div class="form-group">
                                <label for="berangkat_dari">Berangkat dari</label>
                                <input type="text" name="berangkat_dari" id="berangkat_dari" class="form-control"
                                    value="{{ old('berangkat_dari', optional($spt ?? null)->berangkat_dari ?? '') }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="keperluan">Keperluan</label>
                                <input type="text" name="keperluan" class="form-control" value="{{ old('keperluan') }}"
                                    required>

                                <small class="form-text text-muted">
                                    Jika tujuan lebih dari satu (contoh: Kelompok Tani dan Kabupaten/Kota), pisahkan
                                    keperluan dengan tanda <b>;</b>.
                                    <br>
                                    Contoh: <i>Observasi Lapangan; Kunjungan belajar</i> (bagian pertama untuk tujuan 1,
                                    bagian kedua untuk tujuan 2).
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
                                    value="{{ old('tanggal_berangkat') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="tanggal_kembali">Tanggal Kembali</label>
                                <input type="date" id="tanggal_kembali" name="tanggal_kembali" class="form-control"
                                    value="{{ old('tanggal_kembali') }}" required>
                            </div>

                            <!-- ✅ biaya multi item (keterangan + harga per hari) -->
                            <label>Biaya (Keterangan & Harga / Hari)</label>
                            <div id="biayaContainer">
                                <div class="input-group mb-2 biaya-item">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text nomor-biaya">1</span>
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
                            </div>

                            <!-- ✅ hasil hitung -->
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

                            <input type="hidden" name="bulan" value="{{ now()->month }}">
                            <input type="hidden" name="tahun" value="{{ now()->year }}">

                            <div class="form-group">
                                <label>Yang Hadir</label>
                                <input type="text" name="kehadiran" class="form-control"
                                    value="{{ old('kehadiran', optional($spt ?? null)->kehadiran ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="mak">MAK</label>
                                <select name="mak" class="form-control" required>
                                    <option value="">-- Pilih MAK --</option>
                                    <option value="001" {{ old('mak') == '001' ? 'selected' : '' }}>001 - Operasional
                                    </option>
                                    <option value="002" {{ old('mak') == '002' ? 'selected' : '' }}>002 - Perjalanan
                                        Dinas</option>
                                    <option value="003" {{ old('mak') == '003' ? 'selected' : '' }}>003 - Kegiatan
                                        Lapangan</option>
                                    <option value="004" {{ old('mak') == '004' ? 'selected' : '' }}>004 - Administrasi
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="nomor_surat">Nomor Surat</label>
                                <input type="text" name="nomor_surat_awal" class="form-control"
                                    value="{{ old('nomor_surat_awal') }}" placeholder="Isi nomor saja, contoh: 102"
                                    required>
                                <small class="form-text text-muted">
                                    Format akhir: [Nomor]/spt/500.0/{{ $bulanRomawi }}/{{ $tahun }}
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="nomor_kwitansi">Nomor Kwitansi</label>
                                <input type="text" name="nomor_kwitansi_awal" class="form-control"
                                    value="{{ old('nomor_kwitansi_awal') }}" placeholder="Isi nomor saja, contoh: 102"
                                    required>
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
                                <div class="input-group mb-2 arahan-item">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text nomor-arahan">1</span>
                                    </div>
                                    <input type="text" name="arahan[]" class="form-control" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success addArahan">+</button>
                                        <button type="button" class="btn btn-danger removeArahan">−</button>
                                    </div>
                                </div>
                            </div>

                            <label>Masalah / Temuan</label>
                            <div id="masalahContainer">
                                <div class="input-group mb-2 masalah-item">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text nomor-masalah">1</span>
                                    </div>
                                    <input type="text" name="masalah[]" class="form-control" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success addMasalah">+</button>
                                        <button type="button" class="btn btn-danger removeMasalah">−</button>
                                    </div>
                                </div>
                            </div>

                            <label>Saran Tindakan</label>
                            <div id="saranContainer">
                                <div class="input-group mb-2 saran-item">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text nomor-saran">1</span>
                                    </div>
                                    <input type="text" name="saran[]" class="form-control" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success addSaran">+</button>
                                        <button type="button" class="btn btn-danger removeSaran">−</button>
                                    </div>
                                </div>
                            </div>

                            <label>Lain-lain</label>
                            <div id="lainnyaContainer">
                                <div class="input-group mb-2 lainnya-item">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text nomor-lainnya">1</span>
                                    </div>
                                    <input type="text" name="lainnya[]" class="form-control" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success addLainnya">+</button>
                                        <button type="button" class="btn btn-danger removeLainnya">−</button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary prev-step">Kembali</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
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
        // =========================
        // ALERT ERROR DARI LARAVEL
        // =========================
        document.addEventListener("DOMContentLoaded", function() {
            @if ($errors->any())
                alert(@json(implode("\n", $errors->all())));
            @endif
        });

        // =========================
        // HELPER
        // =========================
        function refreshNumber(selector) {
            document.querySelectorAll(selector).forEach((el, i) => {
                el.textContent = i + 1;
            });
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
            return diff > 0 ? diff : -1; // -1 berarti invalid
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

        // =========================
        // CLICK HANDLER (SEMUA ADD/REMOVE)
        // =========================
        document.addEventListener("click", function(e) {

            // =====================
            // PETUGAS (ADD/REMOVE)
            // =====================
            if (e.target.classList.contains("addPetugas")) {
                const template = document.getElementById("petugasTemplate");
                if (template) {
                    document.getElementById("petugasContainer")
                        .appendChild(template.content.cloneNode(true));
                }
            }

            if (e.target.classList.contains("removePetugas")) {
                const row = e.target.closest(".input-group");
                if (row) row.remove();
            }

            // =====================
            // TUJUAN (MAX 2) ADD/REMOVE
            // =====================
            if (e.target.classList.contains("addTujuan")) {
                const container = document.getElementById("tujuanContainer");
                if (!container) return;

                const count = container.querySelectorAll(".tujuan-item").length;
                if (count >= 2) {
                    alert("Maksimal 2 tujuan saja");
                    return;
                }

                const template = document.getElementById("tujuanTemplate");
                if (template) container.appendChild(template.content.cloneNode(true));
            }

            if (e.target.classList.contains("removeTujuan")) {
                const item = e.target.closest(".tujuan-item");
                if (item) item.remove();
            }

            // =====================
            // BIAYA (ADD/REMOVE)
            // =====================
            if (e.target.classList.contains("addBiaya")) {
                const template = document.getElementById("biayaTemplate");
                if (template) {
                    document.getElementById("biayaContainer")
                        .appendChild(template.content.cloneNode(true));
                    refreshNumber(".nomor-biaya");
                    updateBiayaSummary();
                }
            }

            if (e.target.classList.contains("removeBiaya")) {
                const items = document.querySelectorAll(".biaya-item");
                if (items.length > 1) {
                    const row = e.target.closest(".biaya-item");
                    if (row) row.remove();
                    refreshNumber(".nomor-biaya");
                    updateBiayaSummary();
                } else {
                    alert("Minimal 1 biaya wajib ada");
                }
            }

            // =====================
            // ARAHAN (ADD/REMOVE)
            // =====================
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
                if (items.length > 1) {
                    e.target.closest(".arahan-item")?.remove();
                    refreshNumber(".nomor-arahan");
                } else {
                    alert("Minimal 1 arahan wajib ada");
                }
            }

            // =====================
            // MASALAH (ADD/REMOVE)
            // =====================
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
                if (items.length > 1) {
                    e.target.closest(".masalah-item")?.remove();
                    refreshNumber(".nomor-masalah");
                } else {
                    alert("Minimal 1 masalah wajib ada");
                }
            }

            // =====================
            // SARAN (ADD/REMOVE)
            // =====================
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
                if (items.length > 1) {
                    e.target.closest(".saran-item")?.remove();
                    refreshNumber(".nomor-saran");
                } else {
                    alert("Minimal 1 saran wajib ada");
                }
            }

            // =====================
            // LAINNYA (ADD/REMOVE)
            // =====================
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
                if (items.length > 1) {
                    e.target.closest(".lainnya-item")?.remove();
                    refreshNumber(".nomor-lainnya");
                } else {
                    alert("Minimal 1 lainnya wajib ada");
                }
            }
        });

        // =========================
        // CHANGE HANDLER: TUJUAN SELECT (SHOW/HIDE FIELD)
        // =========================
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

        // =========================
        // INPUT HANDLER: BIAYA HARGA
        // =========================
        document.addEventListener("input", function(e) {
            if (e.target.classList.contains("harga-biaya")) updateBiayaSummary();
        });

        // =========================
        // STEP NAV + VALIDASI ALERT
        // =========================
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

                // validasi tanggal khusus step2
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

            // init biaya numbering + summary
            refreshNumber(".nomor-biaya");
            updateBiayaSummary();
        });
    </script>
@endsection
