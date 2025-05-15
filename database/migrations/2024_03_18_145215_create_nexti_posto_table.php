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
        Schema::create('nexti_posto', function (Blueprint $table) {
            $table->string('CDCHAMADA', 50);
            $table->integer('ESTPOS')->index();
            $table->string('POSTRA', 100)->index();
            $table->string('DESPOS', 300);
            $table->string('SERVICO', 200)->nullable();
            $table->integer('VAGAS')->default(0);
            $table->date('DATCRI');
            $table->string('CODOEM', 100)->nullable();
            $table->string('UNIDADE_NEGOCIO', 100)->nullable();
            $table->integer('TIPO_SERVICO')->nullable();
            $table->integer('TABORG')->nullable();
            $table->integer('NUMLOC')->nullable();
            $table->integer('CODFIL')->index();
            $table->integer('NUMEMP')->index();
            $table->date('DATEXT')->nullable();
            $table->string('CODCCU', 250)->nullable();
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('ID')->nullable();
            $table->string('IDEXTERNO', 50)->primary();
            $table->string('CPFCGC', 50)->nullable();
            $table->text('RAZAOSOCIAL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_posto');
    }
};
