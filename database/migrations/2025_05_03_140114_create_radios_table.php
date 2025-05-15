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
            Schema::create('radios', function (Blueprint $table) {
            $table->id('title_id')->primary();
            $table->string('title')->nullable();
            $table->string('soundfile_name')->nullable();
            $table->string('author')->nullable();
            $table->integer('durée(ms)')->nullable();
            $table->time('Durée')->nullable();
            $table->string('interpret')->nullable();
            $table->datetime('last_modif_time')->nullable();
            $table->string('commentaire1')->nullable();
            $table->string('commentaire2')->nullable();
            $table->string('commentaire3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radios');
    }
};
