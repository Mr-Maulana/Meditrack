<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the role enum using a raw query only on MySQL (SQLite doesn't enforce enums)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'apoteker', 'kurir', 'operator', 'dokter') DEFAULT 'apoteker'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the role enum only on MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'apoteker', 'kurir') DEFAULT 'apoteker'");
        }
    }
};
