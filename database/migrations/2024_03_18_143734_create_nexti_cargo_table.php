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
        Schema::create('nexti_cargo', function (Blueprint $table) {
            $table->integer('CDCHAMADA');
            $table->smallInteger('ESTCAR');
            $table->string('CODCAR', 30);
            $table->string('TITCAR', 100);
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('ID')->nullable();
            $table->string('IDEXTERNO', 30)->primary();

            $table->index(
                name: 'NEXTI_CARGO_KEY_INDEX',
                columns: [
                    'ESTCAR',
                    'CODCAR'
                ]
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_cargo');
    }
};
