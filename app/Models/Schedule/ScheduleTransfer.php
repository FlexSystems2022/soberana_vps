<?php

namespace App\Models\Schedule;

use App\Models\People;
use App\Shared\Models\Nexti\NextiModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ScheduleTransfer extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_troca_escala_alterdata';

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
		'TIPCOL',
		'NUMCAD',
		'DATALT',
		'ESCALA',
		'TURMA',
		'TIPO',
		'SITUACAO',
		'OBSERVACAO',
		'ID',
		'IDEXTERNO',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'DATALT' => 'date'
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
     * Format to Log
     * 
     * @return array
     */
    protected function formatToLog(): array
    {
        return [
            'EMPRESA'       => $this->NUMEMP,
            'MATRICULA'     => $this->NUMCAD,
            'COLABORADOR'   => $this->people?->NOMFUN,
            'ESCALA'        => $this->ESCALA,
            'TURMA'         => $this->TURMA,
            'DATA'          => $this->DATALT?->format('d/m/Y') ?? null,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
