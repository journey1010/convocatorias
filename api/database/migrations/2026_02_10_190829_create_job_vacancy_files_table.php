<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_vacancy_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locale_id')->constrained('locales');
            $table->foreignId('job_vancancy_id')->constrained('job_vacancies');
            $table->string('file');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancy_files');
    }
};