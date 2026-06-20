<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sav_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->enum('status', ['Ouvert', 'En cours', 'Résolu', 'Clôturé'])->default('Ouvert');
            $table->enum('priority', ['Normal', 'Critique', 'Urgent'])->default('Normal');
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sav_tickets');
    }
};
