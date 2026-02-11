<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('poktan', function (Blueprint $table) {
            $table->string('nama_poktan', 30)->primary();
            $table->string('ketua', 255);
            $table->string('desa', 100);
            $table->string('kecamatan', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poktan');
    }
};
