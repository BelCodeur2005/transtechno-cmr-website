<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->enum('status', ['Nouveau', 'En cours', 'Devis Envoyé', 'Urgent', 'Gagné', 'Perdu', 'Critique'])->default('Nouveau');
            $table->enum('priority', ['Normal', 'Critique', 'Urgent'])->default('Normal');
            $table->string('responsible_name');
            $table->string('structure_name');
            $table->string('city');
            $table->enum('sector', ['Privé', 'Public', 'ONG', 'Recherche'])->default('Privé');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('specifications')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->boolean('services_installation')->default(false);
            $table->boolean('services_formation')->default(false);
            $table->boolean('services_maintenance')->default(false);
            $table->string('delivery_city')->nullable();
            $table->unsignedBigInteger('transport_cost')->default(0);
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->boolean('vat_exempt')->default(false);
            $table->text('internal_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
