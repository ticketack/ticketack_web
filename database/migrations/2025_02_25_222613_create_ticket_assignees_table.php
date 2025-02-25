<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable(); // Pour potentiellement définir le rôle de chaque assigné
            $table->timestamps();
            
            // Empêcher les doublons
            $table->unique(['ticket_id', 'user_id']);
        });

        // Copier les assignations existantes
        DB::table('tickets')
            ->whereNotNull('assigned_to')
            ->get()
            ->each(function ($ticket) {
                DB::table('ticket_assignees')->insert([
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->assigned_to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        // Supprimer l'ancienne colonne
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
        });

        // Restaurer la première assignation pour chaque ticket
        DB::table('ticket_assignees')
            ->get()
            ->groupBy('ticket_id')
            ->each(function ($assignees, $ticketId) {
                $firstAssignee = $assignees->first();
                DB::table('tickets')
                    ->where('id', $ticketId)
                    ->update(['assigned_to' => $firstAssignee->user_id]);
            });

        Schema::dropIfExists('ticket_assignees');
    }
};
