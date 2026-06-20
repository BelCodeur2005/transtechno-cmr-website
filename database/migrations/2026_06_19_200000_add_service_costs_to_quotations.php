<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('service_installation_cost')->default(0)->after('services_installation');
            $table->unsignedBigInteger('service_formation_cost')->default(0)->after('services_formation');
            $table->unsignedBigInteger('service_maintenance_cost')->default(0)->after('services_maintenance');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['service_installation_cost', 'service_formation_cost', 'service_maintenance_cost']);
        });
    }
};
