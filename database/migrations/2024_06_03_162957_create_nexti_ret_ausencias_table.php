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
        Schema::create('nexti_ret_ausencias', function (Blueprint $table) {
            $table->id();
            $table->datetime('LASTUPDATE')->nullable()->index();
            $table->string('PERSONEXTERNALID', 100)->nullable();
            $table->integer('PERSONID')->nullable();
            $table->text('NOTE')->nullable();
            $table->string('ABSENCESITUATIONEXTERNALID', 100)->nullable();
            $table->integer('ABSENCESITUATIONID')->nullable();
            $table->datetime('FINISHDATETIME')->nullable();
            $table->integer('FINISHMINUTE')->nullable();
            $table->datetime('STARTDATETIME')->nullable();
            $table->integer('STARTMINUTE')->nullable();
            $table->boolean('REMOVED')->nullable();
            $table->integer('USERREGISTERID')->nullable();
            $table->string('CIDCODE', 50)->nullable();
            $table->string('CIDDESCRIPTION')->nullable();
            $table->integer('CIDID')->nullable();
            $table->string('MEDICALDOCTORCRM', 50)->nullable();
            $table->integer('MEDICALDOCTORID')->nullable();
            $table->string('MEDICALDOCTORNAME')->nullable();

            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();

            $table->index(
                name: 'NEXTI_RET_AUSENCIAS_INDEX',
                columns: [
                    'PERSONEXTERNALID',
                    'FINISHDATETIME',
                    'STARTDATETIME',
                    'FINISHMINUTE',
                    'STARTMINUTE',
                    'ABSENCESITUATIONEXTERNALID',
                ]
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_ret_ausencias');
    }
};
