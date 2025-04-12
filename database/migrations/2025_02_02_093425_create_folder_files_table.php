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
        Schema::create('folder_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folder_id');
            $table->string('user_name')->nullable();
            $table->text('description')->nullable();
            $table->string('file');
            $table->string('file_name')->nullable();
            $table->string('file_name_with_extension')->nullable();
            $table->string('file_status')->nullable();
            $table->string('file_type')->nullable();
            $table->string('file_size')->nullable();
            $table->string('file_bunny_id')->nullable();
            $table->string('setting_id')->nullable();
            $table->string('video_resolution')->nullable();
            $table->string('video_duration')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('bunny_status')->nullable();
            $table->integer('view_count')->default(0);

            $table->foreignId('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('folder_id')->references('id')->on('event_folders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folder_files');
    }
};
