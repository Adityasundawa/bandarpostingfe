<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_name');
            $table->string('asset_id');

            // Fields dari API response
            $table->text('title')->nullable();               // caption/judul post
            $table->text('caption')->nullable();             // "Open Drop-down" dll
            $table->string('post_date')->nullable();         // "28 February 10:22" (string dari API)
            $table->string('status')->default('published'); // scheduled | published
            $table->string('post_url')->nullable();          // link ke post (null jika tidak ada)

            // Engagement metrics
            $table->unsignedInteger('reach')->default(0);
            $table->unsignedInteger('likes_reactions')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('shares')->default(0);

            $table->json('raw_data')->nullable();

            // Unique: kombinasi title + date per session + asset (tidak ada post_id dari API)
            $table->string('post_hash')->nullable();  // md5(session+asset+title+date)
            $table->unique(['user_id', 'session_name', 'asset_id', 'post_hash']);

            $table->index(['user_id', 'session_name', 'asset_id', 'status']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_posts');
    }
};
