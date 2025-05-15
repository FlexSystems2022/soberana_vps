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
        Schema::create('nexti_sindicato', function (Blueprint $table) {
            $table->string('CDCHAMADA', 50);
            $table->integer('CODSIN')->index();
            $table->string('NOMSIN', 250);
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('ID')->nullable();
            $table->string('IDEXTERNO', 50)->primary();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_sindicato');
    }
};
