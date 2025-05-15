<?php

namespace App\Models\Paycheck;

use App\Shared\Models\Nexti\NextiModel;

class Event extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_contra_cheque_eventos';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'ID', 'ID_CONTRA_CHEQUE'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'ID',
        'ID_CONTRA_CHEQUE',
        'COST',
        'DESCRIPTION',
        'PAYCHECKRECORDTYPEID',
        'REFERENCE',
        'SITUACAO',
        'LOTE'
    ];

    /**
     * Format to Log
     * 
     * @return array
     */
    protected function formatToLog(): array
    {
        return [];
    }
}
