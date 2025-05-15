<?php

namespace App\Shared\Models\Nexti;

use App\Shared\Models\Model;
use App\Shared\Models\Concerns\HasLogModel;
use App\Shared\Models\Concerns\SendToNexti;
use App\Shared\Models\Concerns\MultiplePrimaryKey;

abstract class NextiModel extends Model
{
    use HasLogModel, SendToNexti, MultiplePrimaryKey;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}