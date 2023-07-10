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
        Schema::create('critaires', function (Blueprint $table) {
            $table->id();
            $table->boolean('Klaxon')->default(true);
            $table->boolean('Essuie_glase')->default(true);
            $table->boolean('Frein')->default(true);
            $table->boolean('Pneu')->default(true);
            $table->boolean('Pare_Brise')->default(true);
            $table->foreignId('detail_enjin_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('critaires');
    }
};
