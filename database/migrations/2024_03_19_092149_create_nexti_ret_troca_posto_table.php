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
        Schema::create('nexti_ret_troca_posto', function (Blueprint $table) {
            $table->id();
            $table->datetime('LASTUPDATE')->nullable();
            $table->string('PERSONEXTERNALID', 100)->nullable();
            $table->integer('PERSONID')->nullable();
            $table->datetime('TRANSFERDATETIME')->nullable();
            $table->string('WORKPLACEEXTERNALID', 100)->nullable();
            $table->string('WORKPLACEID', 100)->nullable();
            $table->boolean('REMOVED')->nullable();
            $table->integer('TIPO')->default(\App\Shared\Enums\TypeEnum::Create->value)->index();
            $table->integer('SITUACAO')->default(\App\Shared\Enums\SituationEnum::Pendent->value)->index();
            $table->text('OBSERVACAO')->nullable();
            $table->integer('USERREGISTERID')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexti_ret_troca_posto');
    }
};
