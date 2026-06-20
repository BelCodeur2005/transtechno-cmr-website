<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pivot : un ticket peut concerner plusieurs équipements du parc installé
        Schema::create('sav_ticket_equipment', function (Blueprint $table) {
            $table->foreignId('sav_ticket_id')->constrained('sav_tickets')->cascadeOnDelete();
            $table->foreignId('equipment_inventory_id')->constrained('equipment_inventory')->cascadeOnDelete();
            $table->primary(['sav_ticket_id', 'equipment_inventory_id']);
        });

        // equipment_id was never added to fresh installs — nothing to migrate or drop
    }

    public function down(): void
    {
        Schema::table('sav_tickets', function (Blueprint $table) {
            $table->foreignId('equipment_id')->nullable()->constrained('equipment_inventory')->nullOnDelete();
        });

        \DB::table('sav_ticket_equipment')->orderBy('sav_ticket_id')->each(function ($pivot) {
            \DB::table('sav_tickets')
                ->where('id', $pivot->sav_ticket_id)
                ->whereNull('equipment_id')
                ->update(['equipment_id' => $pivot->equipment_inventory_id]);
        });

        Schema::dropIfExists('sav_ticket_equipment');
    }
};
