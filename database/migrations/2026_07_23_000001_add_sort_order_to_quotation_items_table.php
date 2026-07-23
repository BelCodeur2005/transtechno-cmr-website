<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0)->after('quotation_id');
        });

        // Initialise sort_order à partir de l'id pour respecter l'ordre de création existant
        DB::statement('UPDATE quotation_items SET sort_order = id');
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
