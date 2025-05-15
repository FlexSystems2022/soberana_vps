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
        Schema::create('nexti_horario', function (Blueprint $table) {
            $table->string('CODHOR', 10)->index();
            $table->string('DESHOR', 100);
            $table->integer('ACTIVE')->nullable();
            $table->integer('SHIFTTYPEID')->nullable();
            $table->time('ENTRADA1')->nullable();
            $table->time('SAIDA1')->nullable();
            $table->time('ENTRADA2')->nullable();
            $table->time('SAIDA2')->nullable();
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
        Schema::dropIfExists('nexti_horario');
    }
};
