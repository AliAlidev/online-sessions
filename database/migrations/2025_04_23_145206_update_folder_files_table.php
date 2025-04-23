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
        Schema::table('folder_files', function (Blueprint $table) {
            $table->string('video_name')->nullable();
            $table->integer('file_order')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folder_files', function (Blueprint $table) {
            $table->dropColumn('video_name');
            $table->dropColumn('file_order');
        });
    }
};
