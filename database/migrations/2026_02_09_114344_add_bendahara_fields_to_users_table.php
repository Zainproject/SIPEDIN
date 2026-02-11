<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // =========================
            // DATA PEJABAT (TTD PEJABAT)
            // =========================
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 50)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'pangkat')) {
                $table->string('pangkat')->nullable()->after('nip');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('pangkat');
            }
            if (!Schema::hasColumn('users', 'masa_bakti')) {
                $table->string('masa_bakti')->nullable()->after('jabatan');
            }

            // role & avatar (kalau belum ada)
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('pejabat')->after('email');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('role');
            }

            // =========================
            // DATA BENDAHARA (TTD BENDAHARA)
            // =========================
            if (!Schema::hasColumn('users', 'bendahara_nama')) {
                $table->string('bendahara_nama')->nullable()->after('masa_bakti');
            }
            if (!Schema::hasColumn('users', 'bendahara_nip')) {
                $table->string('bendahara_nip', 50)->nullable()->after('bendahara_nama');
            }
            if (!Schema::hasColumn('users', 'bendahara_pangkat')) {
                $table->string('bendahara_pangkat')->nullable()->after('bendahara_nip');
            }
            if (!Schema::hasColumn('users', 'bendahara_jabatan')) {
                $table->string('bendahara_jabatan')->nullable()->after('bendahara_pangkat');
            }
            if (!Schema::hasColumn('users', 'bendahara_masa_bakti')) {
                $table->string('bendahara_masa_bakti')->nullable()->after('bendahara_jabatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // drop bendahara fields
            foreach (
                [
                    'bendahara_masa_bakti',
                    'bendahara_jabatan',
                    'bendahara_pangkat',
                    'bendahara_nip',
                    'bendahara_nama',
                ] as $col
            ) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }

            // (optional) jangan hapus pejabat/role/avatar kalau sudah dipakai
            // kalau mau hapus, buka ini:
            /*
            foreach (['masa_bakti','jabatan','pangkat','nip','avatar','role'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
            */
        });
    }
};
