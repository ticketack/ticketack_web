<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Créer la table ticket_assignees si elle n'existe pas
        if (!Schema::hasTable('ticket_assignees')) {
            Schema::create('ticket_assignees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // 2. Vérifier si la colonne assigned_to existe
        try {
            $hasColumn = Schema::hasColumn('tickets', 'assigned_to');
            
            if ($hasColumn) {
                // Récupérer les données seulement si la colonne existe
                $tickets = DB::select('SELECT id, assigned_to FROM tickets WHERE assigned_to IS NOT NULL');

                foreach ($tickets as $ticket) {
                    // Vérifier si l'assignation n'existe pas déjà
                    $exists = DB::table('ticket_assignees')
                        ->where('ticket_id', $ticket->id)
                        ->where('user_id', $ticket->assigned_to)
                        ->exists();

                    if (!$exists) {
                        DB::table('ticket_assignees')->insert([
                            'ticket_id' => $ticket->id,
                            'user_id' => $ticket->assigned_to,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                // Supprimer l'ancienne colonne
                Schema::table('tickets', function (Blueprint $table) {
                    $table->dropForeign(['assigned_to']);
                    $table->dropColumn('assigned_to');
                });
            }
        } catch (\Exception $e) {
            // La colonne n'existe pas, on ne fait rien
        }
    }

    public function down()
    {
        // 1. Recréer la colonne assigned_to si elle n'existe pas
        if (!Schema::hasColumn('tickets', 'assigned_to')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            });

            // 2. Restaurer les données depuis ticket_assignees
            $assignees = DB::table('ticket_assignees')->get();
            foreach ($assignees as $assignee) {
                DB::table('tickets')
                    ->where('id', $assignee->ticket_id)
                    ->update(['assigned_to' => $assignee->user_id]);
            }
        }

        // 3. Supprimer la table ticket_assignees si elle a été créée par cette migration
        if (Schema::hasTable('ticket_assignees')) {
            Schema::dropIfExists('ticket_assignees');
        }
    }
};