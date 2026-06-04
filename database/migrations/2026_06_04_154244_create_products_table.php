<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('reference')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('short_description', 500)->nullable();
            $table->longText('long_description')->nullable();
            $table->unsignedBigInteger('price_fcfa')->nullable();
            $table->boolean('price_visible')->default(false);
            $table->enum('status', ['actif', 'brouillon', 'archivé'])->default('brouillon');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->enum('product_type', ['equipement', 'reactif', 'consommable'])->default('equipement');
            $table->enum('stock_status', ['en_stock', 'en_transit', 'rupture'])->default('en_stock');
            $table->unsignedInteger('stock_quantity')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
