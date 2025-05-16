<?php

namespace App\Models;

use App\Models\Career;
use App\Models\Company;
use App\Shared\Models\Nexti\NextiModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class People extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_colaborador';

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
        'TIPCOL',
        'NUMCAD',
        'NOMFUN',
        'DATANASC',
        'DATADM',
        'DATADEM',
        'CODFIL',
        'CARGO',
        'ESCALA',
        'POSTO',
        'SITFUN',
        'TIPO',
        'SITUACAO',
        'OBSERVACAO',
        'ID',
        'IDEXTERNO',
        'TABORG',
        'NUMLOC',
        'IGNOREVALIDATION',
        'TELEFONE',
        'CELULAR',
        'ENDERECO',
        'NUMERO',
        'BAIRRO',
        'CPF',
        'PIS',
        'EMAIL',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'DATANASC' => 'date',
        'DATADM' => 'date',
        'DATADEM' => 'date'
    ];

    /**
     * Get the career that owns the people.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class, 'CARGO', 'IDEXTERNO');
    }

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
                'CODFIL' => $attributes['CODFIL'],
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
            'COLAB'         => $this->IDEXTERNO,
            'NOME'          => $this->NOMFUN,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
