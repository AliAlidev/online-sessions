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
            $table->string('event_type');  // ['Wedding', 'Engagement', 'Birthday', 'Graduation', 'Conference']
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('customer');
            $table->string('venue');
            $table->text('description')->nullable();
            $table->text('welcome_message')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('profile_picture')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('active_duration')->default(0);
            $table->string('event_link');
            $table->string('event_password')->nullable();
            $table->string('qr_code');
            $table->timestamps();

            // Foreign key constraint for client_id
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
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
