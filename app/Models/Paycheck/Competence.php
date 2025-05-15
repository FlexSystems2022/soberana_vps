<?php

namespace App\Models\Paycheck;

use App\Models\Company;
use App\Shared\Models\Nexti\NextiModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Competence extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_contra_cheque_competencia';

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
        'NUMEMP',
        'IDEXTERNO',
        'ID',
        'NAME',
        'PAYCHECKPERIODDATE',
        'TIPO',
        'SITUACAO',
        'OBSERVACAO',
        'DATPAG'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'PAYCHECKPERIODDATE' => 'date',
        'DATPAG' => 'date'
    ];

    /**
     * Get the company for the people.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function company(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes): Company|null => Company::where([
                'NUMEMP' => $attributes['NUMEMP'],
            ])->first(),
        );
    }

    /**
     * Format to Log
     * 
     * @return array
     */
    protected function formatToLog(): array
    {
        return [
            'EMPRESA'       => $this->NUMEMP,
            'NOME'          => $this->NAME,
            'DATPAG'        => $this->DATPAG?->format('d/m/Y') ?? null,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
