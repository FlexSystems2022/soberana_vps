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
        Schema::table('nexti_contra_cheque_eventos', function (Blueprint $table) {
            $table->string('LOTE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nexti_contra_cheque_eventos', function (Blueprint $table) {
            $table->dropColumn('LOTE');
        });
    }
};
