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
        Schema::create('academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('specialization_area_id')->nullable()->constrained('specialization_areas');
            $table->tinyInteger('level');
            $table->tinyInteger('status');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('description')->nullable();
            $table->string('file', 400);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_records');
    }
};
