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
        Schema::create('job_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('entity_name');
            $table->string('type');
            $table->string('specialization_area');
            $table->tinyInteger('status');
            $table->tinyText('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('file', 400)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_records');
    }
};
