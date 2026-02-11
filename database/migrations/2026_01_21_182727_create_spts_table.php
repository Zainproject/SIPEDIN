<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spts', function (Blueprint $table) {
            $table->id();

            // =====================
            // MULTI DATA (JSON)
            // =====================
            $table->json('petugas');                 // wajib
            $table->json('tujuan');                  // wajib
            $table->json('poktan_nama')->nullable(); // tergantung jenis tujuan
            $table->json('deskripsi_kota')->nullable();
            $table->json('deskripsi_lainnya')->nullable();

            // STEP 4 (wajib)
            $table->json('arahan');
            $table->json('masalah');
            $table->json('saran');
            $table->json('lainnya');

            // =====================
            // DATA UTAMA (wajib)
            // =====================
            $table->string('keperluan');     // wajib
            $table->string('kehadiran');     // wajib
            $table->string('berangkat_dari'); // wajib (baru)

            $table->date('tanggal_berangkat'); // wajib
            $table->date('tanggal_kembali');   // wajib

            // lama (boleh tetap ada untuk kompatibilitas lama)
            $table->decimal('biaya_perhari', 12, 2)->nullable();

            // =====================
            // BIAYA LIST (baru)
            // =====================
            $table->json('keterangan_biaya')->nullable(); // list item biaya
            $table->json('harga_biaya')->nullable();      // list harga per item

            // hasil hitungan (baru)
            $table->integer('lama_hari')->nullable();
            $table->decimal('subtotal_perhari', 14, 2)->nullable();
            $table->decimal('total_biaya', 14, 2)->nullable();

            // =====================
            // ADMINISTRASI (wajib)
            // =====================
            $table->integer('bulan'); // wajib
            $table->integer('tahun'); // wajib
            $table->string('mak');    // wajib

            $table->string('nomor_surat')->unique();    // wajib
            $table->string('nomor_kwitansi')->unique(); // wajib

            $table->string('alat_angkut'); // wajib

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spts');
    }
};
