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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->json('medications')->nullable()->after('patient_id');
            $table->string('medication_name')->nullable()->change();
            $table->string('dosage')->nullable()->change();
            $table->string('frequency')->nullable()->change();
            $table->string('duration')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('medications');
            $table->string('medication_name')->nullable(false)->change();
            $table->string('dosage')->nullable(false)->change();
            $table->string('frequency')->nullable(false)->change();
            $table->string('duration')->nullable(false)->change();
        });
    }
};
