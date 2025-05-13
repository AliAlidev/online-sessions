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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('profile_picture');
            $table->dropColumn('contact_button_text');
            $table->dropColumn('contact_button_link');
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('profile_picture')->nullable();
            $table->string('contact_button_text')->nullable();
            $table->string('contact_button_link')->nullable();
            $table->text('description')->nullable();
        });
    }
};
