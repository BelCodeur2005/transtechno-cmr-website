<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sav_tickets', function (Blueprint $table) {
            $table->text('pre_diagnostic')->nullable()->after('description');
            $table->text('rapport_intervention')->nullable()->after('resolved_at');
        });
    }

    public function down(): void
    {
        Schema::table('sav_tickets', function (Blueprint $table) {
            $table->dropColumn(['pre_diagnostic', 'rapport_intervention']);
        });
    }
};
