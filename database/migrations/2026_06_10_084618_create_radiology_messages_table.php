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
        Schema::create('radiology_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radiology_result_id')->constrained('radiology_results')->onDelete('cascade');
            $table->enum('sender_type', ['staff', 'patient']);
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('channel', ['whatsapp', 'email']);
            $table->text('message_text');
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiology_messages');
    }
};
