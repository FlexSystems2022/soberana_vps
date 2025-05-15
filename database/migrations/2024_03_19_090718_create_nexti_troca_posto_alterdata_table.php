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
        Schema::create('nexti_troca_posto_alterdata', function (Blueprint $table) {
            $table->string('IDTROCADPTO', 100)->nullable();
            $table->integer('NUMEMP');
            $table->integer('TIPCOL');
            $table->string('NUMCAD', 50);
            $table->date('INIATU', 50);
            $table->integer('SEQHIS')->nullable();
            $table->string('POSTO', 50)->index();
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('ID')->nullable();
            $table->string('IDEXTERNO', 100)->primary();
            $table->integer('TABORG')->nullable();
            $table->integer('NUMLOC')->nullable();
            $table->string('CODLOC', 250)->nullable();
            $table->string('CODCCU', 50)->nullable();

            $table->index(
                name: 'NEXTI_TROCA_POSTO_ALTERDATA_COLABORADOR_INDEX',
                columns: [
                    'NUMEMP',
                    'NUMCAD',
                    'TIPCOL'
                ]
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_troca_posto_alterdata');
    }
};
