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
        Schema::create('event_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('folder_name');
            $table->string('folder_type');  // 'image', 'video', 'link
            $table->text('description')->nullable();
            $table->string('folder_thumbnail')->nullable(); // Path to the thumbnail file
            $table->string('folder_link')->nullable();
            // $table->string('bunny_link')->nullable();
            // $table->string('bunny_cdn_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_folders');
    }
};
