<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_files', function (Blueprint $table) {
            // Token unik per file untuk public URL
            $table->string('public_token', 64)->nullable()->unique()->after('stored_name');
            $table->boolean('is_public')->default(false)->after('public_token');
        });
    }

    public function down(): void
    {
        Schema::table('user_files', function (Blueprint $table) {
            $table->dropColumn(['public_token', 'is_public']);
        });
    }
};
