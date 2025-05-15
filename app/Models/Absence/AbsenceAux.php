<?php

namespace App\Models\Absence;

use App\Shared\Models\Model;

class AbsenceAux extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'NEXTI_AUSENCIAS_AUX';

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
        'OBSAFA'
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
}
