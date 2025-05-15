<?php

namespace App\Models\Absence;

use App\Shared\Models\Nexti\NextiModel;

class Situation extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'NEXTI_SITUACAO';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'IDEXTERNO';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'IDESPECIAL',
        'CODSIT',
        'DESSIT',
        'TIPOSIT',
        'TIPO',
        'SITUACAO',
        'OBSERVACAO',
        'ID',
        'IDEXTERNO',
    ];

    /**
     * Format to Log
     * 
     * @return array
     */
    protected function formatToLog(): array
    {
        return [
            'CODIGO DA SITUAÇÃO' => $this->CODSIT,
            'NOME'               => $this->DESSIT,
            'TIPO'               => $this->TIPO->label(),
            'SITUAÇÃO'           => $this->SITUACAO->label(),
            'OBSERVAÇÃO'         => $this->OBSERVACAO
        ];
    }
}
