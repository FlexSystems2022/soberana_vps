<?php

namespace App\Models;

use App\Shared\Models\Nexti\NextiModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_ausencias_alterdata';

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
        'IDAFASTAMENTO',
        'NUMEMP',
        'TIPCOL',
        'NUMCAD',
        'ABSENCESITUATIONEXTERNALID',
        'FINISHDATETIME',
        'STARTDATETIME',
        'IDEXTERNO',
        'CIDCODE',
        'CIDDESCRICAO',
        'CIDID',
        'DOUTOR_CRM',
        'DOUTOR_NOME',
        'DOUTOR_ID',
        'OBSAFA',
        'ID',
        'TIPO',
        'SITUACAO',
        'OBSERVACAO',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'FINISHDATETIME' => 'date',
        'STARTDATETIME' => 'date'
    ];

    /**
     * Get the people for the schedule transfer.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function people(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes): People|null => People::where([
                'NUMEMP' => $attributes['NUMEMP'],
                'TIPCOL' => $attributes['TIPCOL'],
                'NUMCAD' => $attributes['NUMCAD'],
            ])->first(),
        );
    }

    /**
     * Get the situation that has the paycheck.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function situation(): BelongsTo
    {
        return $this->belongsTo(Absence\Situation::class, 'ABSENCESITUATIONEXTERNALID', 'IDEXTERNO');
    }

    /**
     * Format to Log
     * 
     * @return array
     */
    protected function formatToLog(): array
    {
        return [
            'EMPRESA'                     => $this->NUMEMP,
            'MATRICULA'                   => $this->NUMCAD,
            'COLABORADOR'                 => $this->people?->NOMFUN,
            'SITUAÇÃO AFASTAMENTO'        => $this->situation->DESSIT,
            'DATA INICIO'                 => $this->STARTDATETIME?->format('d/m/Y H:i') ?? null,
            'DATA FIM'                    => $this->FINISHDATETIME?->format('d/m/Y H:i') ?? null,
            'TIPO'                        => $this->TIPO->label(),
            'SITUAÇÃO'                    => $this->SITUACAO->label(),
            'OBSERVAÇÃO'                  => $this->OBSERVACAO
        ];
    }
}
