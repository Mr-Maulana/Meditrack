<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('notes');
            $table->string('recipient_relation')->nullable()->after('recipient_name');
            $table->string('recipient_phone')->nullable()->after('recipient_relation');
            $table->string('signature')->nullable()->after('proof_image');
            $table->timestamp('arrived_at')->nullable()->after('delivered_at');
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'recipient_relation', 
                'recipient_phone',
                'signature',
                'arrived_at'
            ]);
        });
    }
};