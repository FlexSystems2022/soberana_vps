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
        Schema::create('nexti_ret_posto', function (Blueprint $table) {
            $table->id();
            $table->string('EXTERNALID', 50)->nullable();
            $table->string('NAME', 250);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_ret_posto');
    }
};
