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
        Schema::create('nexti_ausencias_alterdata', function (Blueprint $table) {
            $table->string('IDAFASTAMENTO')->nullable();
            $table->integer('NUMEMP');
            $table->integer('TIPCOL');
            $table->string('NUMCAD', 50);
            $table->string('ABSENCESITUATIONEXTERNALID')->index();
            $table->dateTime('FINISHDATETIME')->nullable();
            $table->dateTime('STARTDATETIME')->nullable();
            $table->string('IDEXTERNO', 100)->primary();
            $table->string('CIDCODE')->nullable();
            $table->string('CIDDESCRICAO')->nullable();
            $table->bigInteger('CIDID')->nullable();
            $table->string('DOUTOR_CRM')->nullable();
            $table->string('DOUTOR_NOME')->nullable();
            $table->bigInteger('DOUTOR_ID')->nullable();
            $table->text('OBSAFA')->nullable();
            $table->integer('ID')->nullable();
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();

            $table->index(
                name: 'NEXTI_AUSENCIAS_COLABORADOR_INDEX',
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
        Schema::dropIfExists('nexti_ausencias_alterdata');
    }
};
