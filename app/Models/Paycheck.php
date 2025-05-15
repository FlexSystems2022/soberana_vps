<?php

namespace App\Models;

use App\Models\People;
use App\Shared\Models\Nexti\NextiModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paycheck extends NextiModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nexti_contra_cheque';

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
		'BASEFGTS',
		'BASEINSS',
		'GROSSPAY',
		'MONTHFGTS',
		'ID',
		'IDEXTERNO',
		'TIPO',
		'SITUACAO',
		'OBSERVACAO',
		'CONTRA_CHEQUE_CMP'
    ];

    /**
     * Get the people for the paycheck.
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
     * Get the competence that owns the paycheck.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function competence(): BelongsTo
    {
        return $this->belongsTo(Paycheck\Competence::class, 'CONTRA_CHEQUE_CMP', 'IDEXTERNO');
    }

    /**
     * Get the events that has the paycheck.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function events(): HasMany
    {
        return $this->hasMany(Paycheck\Event::class, 'ID_CONTRA_CHEQUE', 'IDEXTERNO');
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
            'COMPETENCIA'   => $this->competence?->NAME,
            'DATPAG'        => $this->competence?->DATPAG?->format('d/m/Y') ?? null,
            'TIPO'          => $this->TIPO->label(),
            'SITUAÇÃO'      => $this->SITUACAO->label(),
            'OBSERVAÇÃO'    => $this->OBSERVACAO
        ];
    }
}
