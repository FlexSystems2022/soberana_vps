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
        Schema::create('nexti_contra_cheque', function (Blueprint $table) {
            $table->integer('NUMEMP');
            $table->integer('TIPCOL');
            $table->string('NUMCAD', 50);
            $table->decimal('BASEFGTS', 14, 6)->default(0);
            $table->decimal('BASEINSS', 14, 6)->default(0);
            $table->decimal('GROSSPAY', 14, 6)->default(0);
            $table->decimal('MONTHFGTS', 14, 6)->default(0);
            $table->integer('ID')->nullable();
            $table->string('IDEXTERNO', 100)->primary();
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->string('CONTRA_CHEQUE_CMP')->index();

            $table->index(
                name: 'NEXTI_CONTRA_CHEQUE_COLABORADOR_INDEX',
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
        Schema::dropIfExists('nexti_contra_cheque');
    }
};
