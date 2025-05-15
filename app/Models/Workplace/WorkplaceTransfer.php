<?php

namespace App\Models\Workplace;

use App\Models\People;
use App\Models\Workplace;
use App\Shared\Models\Nexti\NextiModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkplaceTransfer extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_troca_posto_alterdata';

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
        'IDTROCADPTO',
        'NUMEMP',
        'TIPCOL',
        'NUMCAD',
        'INIATU',
        'SEQHIS',
        'POSTO',
        'TIPO',
        'SITUACAO',
        'OBSERVACAO',
        'ID',
        'IDEXTERNO',
        'TABORG',
        'NUMLOC',
        'CODLOC',
        'CODCCU'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'INIATU' => 'date'
    ];

    /**
     * Get the workplace that owns the workplace transfer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function workplace(): BelongsTo
    {
        return $this->belongsTo(Workplace::class, 'POSTO', 'IDEXTERNO');
    }

    /**
     * Get the people for the workplace transfer.
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
            'POSTO'         => $this->workplace?->DESPOS,
            'DATA'          => $this->INIATU?->format('d/m/Y') ?? null,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
