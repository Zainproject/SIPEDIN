<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('action'); // visit, create, update, delete, print, login
            $table->string('method', 10)->nullable();
            $table->string('url')->nullable();
            $table->string('route')->nullable();

            $table->json('payload')->nullable(); // simpan JSON, lebih rapi daripada text

            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();

            $table->index(['user_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
