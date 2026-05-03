<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Tracking fields
            $table->decimal('current_latitude', 10, 8)->nullable()->after('longitude');
            $table->decimal('current_longitude', 11, 8)->nullable()->after('current_latitude');
            $table->decimal('distance_traveled', 8, 2)->nullable()->after('current_longitude')->comment('in km');
            $table->timestamp('departure_time')->nullable()->after('distance_traveled');
            $table->timestamp('arrival_time')->nullable()->after('departure_time');
            
            // Delivery proof fields
            $table->string('receiver_name')->nullable()->after('proof_image');
            $table->string('receiver_phone')->nullable()->after('receiver_name');
            $table->string('receiver_signature')->nullable()->after('receiver_phone');
            $table->text('delivery_notes')->nullable()->after('receiver_signature');
            $table->string('delivery_status')->default('pending')->after('status')->comment('pending, in_transit, arrived, delivered');
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'current_latitude',
                'current_longitude',
                'distance_traveled',
                'departure_time',
                'arrival_time',
                'receiver_name',
                'receiver_phone',
                'receiver_signature',
                'delivery_notes',
                'delivery_status'
            ]);
        });
    }
};