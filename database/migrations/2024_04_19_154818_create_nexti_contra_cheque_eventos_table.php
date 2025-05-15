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
        Schema::create('nexti_contra_cheque_eventos', function (Blueprint $table) {
            $table->string('ID')->index();
            $table->string('ID_CONTRA_CHEQUE')->index();
            $table->decimal('COST', 14, 6)->default(0);
            $table->string('DESCRIPTION', 250)->nullable();
            $table->integer('PAYCHECKRECORDTYPEID')->nullable();
            $table->string('REFERENCE', 10)->nullable();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();

            $table->primary([
                'ID_CONTRA_CHEQUE',
                'ID'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_contra_cheque_eventos');
    }
};
