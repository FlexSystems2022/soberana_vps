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
        Schema::create('nexti_colaborador', function (Blueprint $table) {
            $table->string('CDCHAMADA', 50);
            $table->integer('NUMEMP');
            $table->integer('TIPCOL');
            $table->string('NUMCAD', 50);
            $table->string('NOMFUN', 150);
            $table->date('DATANASC')->nullable();
            $table->date('DATADM')->nullable();
            $table->date('DATADEM')->nullable();
            $table->integer('CODFIL')->nullable();
            $table->string('CARGO', 40)->nullable()->index();
            $table->string('ESCALA', 40)->nullable();
            $table->string('POSTO', 40)->nullable();
            $table->integer('SITFUN')->default(0);
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('ID')->nullable();
            $table->string('IDEXTERNO', 50)->primary();
            $table->integer('TABORG')->nullable();
            $table->integer('NUMLOC')->nullable();
            $table->char('IGNOREVALIDATION', 1)->nullable();
            $table->string('TELEFONE', 50)->nullable();
            $table->string('CELULAR', 50)->nullable();
            $table->string('ENDERECO', 50)->nullable();
            $table->string('NUMERO', 50)->nullable();
            $table->string('BAIRRO', 50)->nullable();
            $table->string('CPF', 50)->nullable();
            $table->string('PIS', 50)->nullable();
            $table->string('EMAIL', 100)->nullable();

            $table->index(
                name: 'NEXTI_COLABORADOR_COLABORADOR',
                columns: [
                    'NUMEMP',
                    'NUMCAD',
                    'TIPCOL'
                ]
            );

            $table->index(
                name: 'NEXTI_COLABORADOR_EMPRESA',
                columns: [
                    'CODFIL',
                    'NUMEMP'
                ]
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_colaborador');
    }
};
