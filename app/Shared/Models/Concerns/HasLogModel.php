<?php

namespace App\Shared\Models\Concerns;

use App\Shared\Models\Model;

trait HasLogModel
{
    /**
     * Format to Log
     * 
     * @return array
     */
    protected abstract function formatToLog(): array;

    /**
     * Get to Log
     * 
     * @param callback $action
     * @return array
     */
    public static function getToLog(callable $action=null): array
    {
        $query = static::query();

        if($action) {
            $action($query);
        }

        return $query->get()->map(fn(Model $model): array => $model->formatToLog())->toArray();
    }
}
