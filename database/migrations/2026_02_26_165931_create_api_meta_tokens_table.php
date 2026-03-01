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
        Schema::create('api_meta_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel users
            $table->string('token')->unique();
            $table->string('client_name')->nullable();
            $table->string('role')->default('client');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expired_at')->nullable();
            $table->json('sessions')->nullable(); // Simpan array session dalam format JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_meta_tokens');
    }
};
