<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reagent_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('storage_temp', ['Ambiante', '+2-8°C', '-20°C', '-80°C'])->default('Ambiante');
            $table->string('conditionnement')->nullable();
            $table->string('volume_quantity')->nullable();
            $table->boolean('is_ivd')->default(false);
            $table->string('lot_number_example')->nullable();
            $table->string('expiry_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reagent_details');
    }
};
