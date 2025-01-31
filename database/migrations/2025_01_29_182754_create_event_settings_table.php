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
        Schema::create('event_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->boolean('image_share_guest_book')->default(false);
            $table->boolean('image_folders')->default(false);
            $table->boolean('video_playlist')->default(false);
            $table->boolean('allow_upload')->default(false);
            $table->boolean('auto_image_approve')->default(false);
            $table->boolean('allow_image_download')->default(false);
            $table->string('theme')->nullable();
            $table->string('accent_color')->nullable();
            $table->string('font')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_settings');
    }
};
