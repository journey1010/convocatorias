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
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('province_id')->constrained('provinces');
            $table->foreignId('district_id')->constrained('districts');
            $table->string('address');
            $table->date('birthday');
            $table->tinyInteger('genere');
            $table->boolean('have_cert_disability');
            $table->string('file_cert_disability', 400)->nullable();
            $table->boolean('have_cert_army');
            $table->string('file_cert_army', 400)->nullable();
            $table->boolean('have_cert_professional_credentials');
            $table->string('file_cert_professional_credentials', 400)->nullable();
            $table->boolean('is_active_cert_professional_credentials');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_data_extra');
    }
};