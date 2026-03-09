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
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->default(0);
            $table->string('extension', 20)->nullable();
            $table->text('description')->nullable();
            $table->string('public_token', 64)->nullable()->unique();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->foreign('folder_id')->references('id')->on('user_folders')->nullOnDelete();
            $table->index(['user_id', 'folder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_files');
    }
};
