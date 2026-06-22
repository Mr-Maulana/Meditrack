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
            if (!Schema::hasColumn('radiology_results', 'image_path')) {
                $table->string('image_path')->nullable()->after('doctor_id');
            }
            if (!Schema::hasColumn('radiology_results', 'preview_image_path')) {
                $table->string('preview_image_path')->nullable()->after('image_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radiology_results', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'preview_image_path']);
        });
    }
};
