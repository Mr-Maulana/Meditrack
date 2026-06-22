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
            if (!Schema::hasColumn('radiology_results', 'dicom_file_path')) {
                $table->string('dicom_file_path')->nullable()->default(null);
            } else {
                // Make it nullable and set default null if column exists
                $table->string('dicom_file_path')->nullable()->default(null)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radiology_results', function (Blueprint $table) {
            // Optionally drop the column if it was added by this migration
            if (Schema::hasColumn('radiology_results', 'dicom_file_path')) {
                $table->dropColumn('dicom_file_path');
            }
        });
    }
};
