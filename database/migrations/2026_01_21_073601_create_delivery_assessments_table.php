<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->foreignId('courier_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('arrival_time')->nullable();
            $table->timestamp('handover_time')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('estimated_minutes')->nullable();
            $table->integer('actual_minutes')->nullable();
            $table->enum('patient_condition', ['baik', 'sedang', 'buruk'])->default('baik');
            $table->boolean('medication_understood')->default(false);
            $table->boolean('side_effects_explained')->default(false);
            $table->text('patient_feedback')->nullable();
            $table->text('special_notes')->nullable();
            $table->string('handover_photo')->nullable();
            $table->string('signature_image')->nullable();
            $table->enum('assessment_status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_assessments');
    }
};