<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->index(['courier_id', 'status'], 'deliveries_courier_status_index');
            $table->index('delivery_date', 'deliveries_delivery_date_index');
            $table->index('delivered_at', 'deliveries_delivered_at_index');
            $table->index(['status', 'delivery_date'], 'deliveries_status_date_index');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->index('created_by', 'patients_created_by_index');
            $table->index('created_at', 'patients_created_at_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_id', 'read_at'], 'notifications_notifiable_read_index');
            $table->index('created_at', 'notifications_created_at_index');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->index('created_at', 'prescriptions_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex('deliveries_courier_status_index');
            $table->dropIndex('deliveries_delivery_date_index');
            $table->dropIndex('deliveries_delivered_at_index');
            $table->dropIndex('deliveries_status_date_index');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('patients_created_by_index');
            $table->dropIndex('patients_created_at_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_read_index');
            $table->dropIndex('notifications_created_at_index');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropIndex('prescriptions_created_at_index');
        });
    }
};
