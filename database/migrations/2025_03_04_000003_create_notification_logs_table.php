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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('notification_type_id')->constrained()->onDelete('cascade');
            $table->string('channel'); // 'in_app', 'email', 'sms'
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable(); // Stocke des informations supplémentaires (ID du ticket, etc.)
            $table->timestamp('sent_at');
            $table->string('status')->default('sent'); // 'sent', 'delivered', 'failed'
            $table->text('error')->nullable(); // En cas d'échec
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
