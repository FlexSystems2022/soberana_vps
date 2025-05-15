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
        Schema::create('nexti_ret_troca_escala', function (Blueprint $table) {
            $table->id();
            $table->dateTime('LASTUPDATE')->nullable();
            $table->string('PERSONEXTERNALID', 100)->nullable();
            $table->integer('PERSONID')->nullable();
            $table->integer('ROTATIONCODE')->nullable();
            $table->dateTime('TRANSFERDATETIME')->nullable();
            $table->string('SCHEDULEEXTERNALID', 100)->nullable();
            $table->integer('SCHEDULEID')->nullable();
            $table->integer('ROTATIONID')->nullable();
            $table->integer('REMOVED')->nullable();
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('USERREGISTERID')->nullable();

            $table->index(
                name: 'NEXTI_RET_TROCA_ESCALA_ALTERDATA_INDEX',
                columns: [
                    'PERSONEXTERNALID',
                    'SCHEDULEID',
                    'TRANSFERDATETIME',
                    'ROTATIONCODE'
                ]
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_ret_troca_escala');
    }
};
