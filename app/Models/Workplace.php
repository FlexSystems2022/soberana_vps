<?php

namespace App\Models;

use App\Shared\Models\Nexti\NextiModel;

class Workplace extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'NEXTI_POSTO';

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
        'CDCHAMADA',
        'ESTPOS',
        'POSTRA',
        'DESPOS',
        'SERVICO',
        'VAGAS',
        'DATCRI',
        'CODOEM',
        'UNIDADE_NEGOCIO',
        'TIPO_SERVICO',
        'TABORG',
        'NUMLOC',
        'CODFIL',
        'NUMEMP',
        'DATEXT',
        'CODCCU',
        'TIPO',
        'SITUACAO',
        'OBSERVACAO',
        'ID',
        'IDEXTERNO',
        'CPFCGC',
        'RAZAOSOCIAL',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'DATCRI' => 'date',
        'DATEXT' => 'date'
    ];

    /**
     * Format to Log
     * 
     * @return array
     */
    protected function formatToLog(): array
    {
        return [
            'ESTPOS'        => $this->ESTPOS,
            'POSTRA'        => $this->POSTRA,
            'TABORG'        => $this->TABORG,
            'CODLOC'        => null,
            'DESPOS'        => $this->DESPOS,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
