<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Métadonnées de référencement. Si vides, on retombe sur title / excerpt côté front.
            $table->string('meta_title')->nullable()->after('excerpt');
            $table->string('meta_description', 320)->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });
    }
};
