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
        Schema::create('nexti_contra_cheque_competencia', function (Blueprint $table) {
            $table->integer('NUMEMP')->index();
            $table->string('IDEXTERNO', 100)->primary();
            $table->integer('ID')->nullable();
            $table->string('NAME', 200)->nullable();
            $table->date('PAYCHECKPERIODDATE')->nullable();
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->date('DATPAG')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_contra_cheque_competencia');
    }
};
