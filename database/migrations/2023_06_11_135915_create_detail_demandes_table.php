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
        Schema::create('detail_demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('famille_enjin_id')->constrained();;
            $table->foreignId('demande_id')->constrained();
            $table->string('Description');
            $table->integer('qte');
            $table->boolean('effect')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_demandes');
    }
};
