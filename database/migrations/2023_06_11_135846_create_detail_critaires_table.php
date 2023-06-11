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
        Schema::create('detail_critaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('famille_enjin_id')->constrained();;
            $table->foreignId('critaire_id')->constrained();
            $table->string('Commentaire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_critaires');
    }
};
