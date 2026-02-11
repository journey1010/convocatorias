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
        Schema::create('job_vacancy_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_vacancy_id')->constrained('job_vacancies')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que realizó el cambio');
            $table->string('action', 50)->comment('Tipo de acción: created, updated, status_changed, etc.');
            $table->json('old_values')->nullable()->comment('Valores anteriores');
            $table->json('new_values')->comment('Valores nuevos');
            $table->json('changed_fields')->nullable()->comment('Campos modificados');
            $table->string('ip_address', 45)->nullable()->comment('Dirección IP del usuario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_vacancy_edit_logs');
    }
};
