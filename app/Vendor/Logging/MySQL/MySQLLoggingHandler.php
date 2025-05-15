<?php

namespace App\Vendor\Logging\MySQL;

use Monolog\LogRecord;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\AbstractProcessingHandler;

class MySQLLoggingHandler extends AbstractProcessingHandler
{
    protected string $table = 'logs';

    /**
     * Writes the (already formatted) record down to the log of the implementing handler
     */
    protected function write(LogRecord $record): void
    {
        DB::table($this->table)->insert([
            'message'         => $record->message,
            'context'         => json_encode($record->context),
            'level'           => $record->level->value,
            'level_name'      => $record->level->name,
            'channel'         => $record->channel,
            'record_datetime' => $record->datetime->format('Y-m-d H:i:s'),
            'extra'           => json_encode($record->extra),
            'formatted'       => $record->formatted,
            'remote_addr'     => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent'      => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);     
    }
}