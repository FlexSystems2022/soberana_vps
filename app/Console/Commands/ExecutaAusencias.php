<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExecutaAusencias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExecutaAusencias';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa todos os processos de afastamentos em sequência';

    public function handle()
    {
        $key = time();
        Log::info("{$key} - Início " . static::class);

        $commands = [
            Intermediary\Insert\InsertAusencia::class,
            function() {
                $this->call('nexti-sync', ['folder' => 'absence']);
            },
            Nexti\ProcessaAusencia::class,
            Nexti\Get\BuscaAusencia::class,
            Intermediary\Merge\MergeAusencia::class,
        ];

        foreach ($commands as $command) {
            if (is_callable($command)) {
                $command(); 
            } else {
                $this->call($command);
            }
        }

        Log::info("{$key} - Fim " . static::class);

        return static::SUCCESS;
    }
}
