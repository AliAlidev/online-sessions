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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('planner_name');
            $table->string('planner_business_name')->nullable();
            $table->string('phone_number');
            $table->string('email');
            $table->string('client_role');
            $table->string('logo')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('contact_button_text')->nullable();
            $table->string('contact_button_link')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
