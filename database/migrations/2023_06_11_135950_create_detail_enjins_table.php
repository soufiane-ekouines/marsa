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
        Schema::create('detail_enjins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enjin_id')->constrained();;
            $table->foreignId('demande_id')->constrained();
            $table->date('date_sortie');
            $table->date('date_entrer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_enjins');
    }
};
