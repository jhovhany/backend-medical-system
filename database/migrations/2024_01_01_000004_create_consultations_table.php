<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')
                ->constrained('medical_records')
                ->cascadeOnDelete();
            $table->foreignId('doctor_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamp('consultation_date');
            $table->text('reason');
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['medical_record_id', 'consultation_date']);
            $table->index(['doctor_id', 'consultation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
