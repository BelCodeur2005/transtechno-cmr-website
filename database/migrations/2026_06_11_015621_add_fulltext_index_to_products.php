<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index FULLTEXT exigé par Scout (driver database) pour MATCH ... AGAINST
        // sur l'ensemble exact (name, short_description, long_description).
        Schema::table('products', function (Blueprint $table) {
            $table->fullText(['name', 'short_description', 'long_description'], 'products_fulltext');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText('products_fulltext');
        });
    }
};
