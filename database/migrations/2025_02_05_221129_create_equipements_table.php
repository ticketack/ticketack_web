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
        Schema::create('equipements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('designation', 255);
            $table->string('marque', 255);
            $table->string('modele', 255);
            $table->string('image', 255)->nullable();
            $table->bigInteger('icone')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('equipements')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
