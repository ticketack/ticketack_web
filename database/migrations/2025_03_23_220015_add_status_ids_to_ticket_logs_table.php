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
        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('old_status_id')->nullable()->after('type');
            $table->unsignedBigInteger('new_status_id')->nullable()->after('old_status_id');
            
            $table->foreign('old_status_id')->references('id')->on('ticket_statuses')->onDelete('set null');
            $table->foreign('new_status_id')->references('id')->on('ticket_statuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->dropForeign(['old_status_id']);
            $table->dropForeign(['new_status_id']);
            $table->dropColumn(['old_status_id', 'new_status_id']);
        });
    }
};
