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
        Schema::create('nexti_data_log_email', function (Blueprint $table) {
            $table->dateTime('DATA_INICIO');
            $table->dateTime('DATA_FIM')->nullable();
            $table->string('MSG', 250)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_data_log_email');
    }
};
