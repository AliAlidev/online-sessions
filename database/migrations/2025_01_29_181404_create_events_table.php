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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->string('event_alias_name')->nullable();
            $table->string('bunny_event_name');
            $table->unsignedBigInteger('event_type_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('customer')->nullable();
            $table->string('venue')->nullable();
            $table->text('description')->nullable();
            $table->text('welcome_message')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('profile_picture')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('active_duration')->nullable();
            $table->string('event_link');
            $table->string('event_password')->nullable();
            $table->string('qr_code');
            $table->string('bunny_main_folder_name')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->string('video_collection_id')->nullable();
            $table->timestamps();

            // Foreign key constraint for client_id
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
