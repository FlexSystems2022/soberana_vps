<?php

namespace App\Models;

use App\Shared\Models\Model;
use Illuminate\Support\Carbon;

class MailLog extends Model
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
    protected $table = 'nexti_data_log_email';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'DATA_INICIO',
        'DATA_FIM',
        'MSG',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'DATA_INICIO' => 'datetime',
        'DATA_FIM' => 'datetime'
    ];

    /**
     * Update Log
     * 
     * @return bool
     */
    public static function updateDatetime(): bool
    {
        return static::query()->update([
            'DATA_INICIO' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get Last Log
     * 
     * @return \Illuminate\Support\Carbon|null
     **/
    public static function getLastLog(): Carbon|null
    {
        $log = static::firstOrCreate(values: [
            'DATA_INICIO' => date('Y-m-d H:i:s'),
            'DATA_FIM' => date('Y-m-d H:i:s')
        ]);

        return $log->DATA_INICIO;
    }

    /**
     * Check Log
     * 
     * @return bool
     **/
    public static function checkSendLog(): bool
    {
        $now = Carbon::now();
        
        if($now->format('H') <= 5) {
            return false;
        }

        $last = static::getLastLog();

        return $last->format('Y-m-d') !== $now->format('Y-m-d');
    }
}
