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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profession')->nullable()->after('role');
            $table->string('employee_id')->nullable()->after('profession'); // NIP/ID Pegawai
            $table->text('address')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profession', 'employee_id', 'address', 'gender']);
        });
    }
};
