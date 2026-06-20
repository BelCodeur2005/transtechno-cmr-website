<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pole_project_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('structure')->nullable();
            $table->string('city')->nullable();
            $table->enum('pole_type', ['hemodialyse', 'abm', 'autre'])->default('hemodialyse');
            $table->text('message');
            $table->enum('status', ['nouveau', 'en_cours', 'traité'])->default('nouveau');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pole_project_requests');
    }
};
