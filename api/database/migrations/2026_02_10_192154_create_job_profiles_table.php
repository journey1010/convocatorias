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
        Schema::create('job_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('job_vacancy_id')->constrained('job_vacancies');
            $table->string('title');
            $table->string('salary', 10);
            $table->foreignId('office_id')->constrained('offices');
            $table->string('code_profile')->nullable();
            $table->string('file', 400);
            $table->string('file_name', 400);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_profiles');
    }
};
