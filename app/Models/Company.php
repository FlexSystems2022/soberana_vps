<?php

namespace App\Models;

use App\Shared\Models\Nexti\NextiModel;

class Company extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'NEXTI_EMPRESA';

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
        'CDEMPRESA',
        'NUMEMP',
        'CODFIL',
        'RAZSOC',
        'NOMFIL',
        'CNPJ',
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
            'EMPRESA'       => $this->NUMEMP,
            'NOME'          => $this->RAZSOC,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
