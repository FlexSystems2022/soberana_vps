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
        Schema::create('nexti_empresa', function (Blueprint $table) {
            $table->bigInteger('CDEMPRESA')->default(0);
            $table->bigInteger('NUMEMP')->default(0);
            $table->integer('CODFIL');
            $table->string('RAZSOC', 100);
            $table->string('NOMFIL', 100)->nullable();
            $table->string('CNPJ', 14)->nullable();
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('ID')->nullable();
            $table->string('IDEXTERNO', 50)->primary();

            $table->index(
                name: 'nexti_empresa_numemp_codfil_index',
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
        Schema::dropIfExists('nexti_empresa');
    }
};
