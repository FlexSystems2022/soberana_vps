<?php

namespace App\Models;

use App\Shared\Models\Nexti\NextiModel;

class Shift extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_horario';

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
        'CODHOR',
        'DESHOR',
        'ACTIVE',
        'SHIFTTYPEID',
        'ENTRADA1',
        'SAIDA1',
        'ENTRADA2',
        'SAIDA2',
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
            'HORARIO'       => $this->CODHOR,
            'NOME'          => $this->DESHOR,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
