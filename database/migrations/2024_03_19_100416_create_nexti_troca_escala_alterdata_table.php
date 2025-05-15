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
        Schema::create('nexti_troca_escala_alterdata', function (Blueprint $table) {
            $table->integer('NUMEMP');
            $table->integer('TIPCOL');
            $table->string('NUMCAD', 50);
            $table->date('DATALT');
            $table->string('ESCALA', 30);
            $table->integer('TURMA')->nullable()->default(1);
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('ID')->nullable();
            $table->string('IDEXTERNO', 30)->primary();

            $table->index(
                name: 'NEXTI_TROCA_ESCALA_ALTERDATA_COLABORADOR_INDEX',
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
        Schema::dropIfExists('nexti_troca_escala_alterdata');
    }
};
