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
      Schema::create('meta_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_name');
            $table->string('asset_id');
            $table->string('page_name')->nullable();
            $table->string('category')->nullable();
            $table->string('picture')->nullable();
            $table->json('raw_data')->nullable(); // simpan full response dari API
            $table->timestamps();

            // Unique: 1 asset_id per session per user
            $table->unique(['user_id', 'session_name', 'asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_assets');
    }
};
