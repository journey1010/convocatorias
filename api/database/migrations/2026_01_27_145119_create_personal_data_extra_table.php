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
        Schema::create('personal_data_extra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->datetimes('birthday');
            $table->tinyInteger('genere');
            $table->boolean('have_cert_discapacity');
            $table->boolean('have_cert_army');
            $table->boolean('have_cert_professional_credentials');
            $table->boolean('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_data_extra');
    }
};