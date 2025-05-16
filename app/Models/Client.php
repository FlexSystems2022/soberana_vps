<?php

namespace App\Models;

use App\Shared\Models\Nexti\NextiModel;

class Client extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_cliente';

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
        'NUMEMP',
        'CODOEM',
        'NOMOEM',
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
            'CLIENTE'       => $this->CODOEM,
            'NOME'          => $this->NOMOEM,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
