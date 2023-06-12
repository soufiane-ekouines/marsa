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
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->date('date_demande');
            $table->string('Shift');
            $table->date('Sortie_preveue');
            $table->foreignId('entite_id')->constrained();;
            $table->foreignId('user_id')->constrained();;
            $table->string('Commentaire');
            $table->string('etat')->default('restant');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
