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
        Schema::create('enjins', function (Blueprint $table) {
            $table->id();
            $table->string('Nom_enjin');
            $table->string('Modele_enjin');
            $table->string('Matricule');
            $table->integer('Kilometrage');
            $table->string('Etat');
            $table->string('Commentaire');
            $table->foreignId('famille_enjin_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enjins');
    }
};
