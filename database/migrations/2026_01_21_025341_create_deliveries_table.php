<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained();
            $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
            $table->foreignId('courier_id')->nullable()->constrained('users');
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'on_delivery', 'delivered', 'failed'])->default('pending');
            $table->text('delivery_address');
            $table->date('delivery_date');
            $table->text('notes')->nullable();
            $table->string('proof_image')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
};