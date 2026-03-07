<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('path');             // support subfolder: 'meta', 'meta/isco', 'meta/isco/sd'
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('extension')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_files');
    }
};
