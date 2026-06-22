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
        Schema::table('radiology_results', function (Blueprint $table) {
            if (Schema::hasColumn('radiology_results', 'dicom_file_path')) {
                $table->dropColumn('dicom_file_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radiology_results', function (Blueprint $table) {
            $table->string('dicom_file_path')->nullable();
        });
    }
};
