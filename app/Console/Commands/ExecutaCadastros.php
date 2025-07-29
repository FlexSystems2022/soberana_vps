<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExecutaCadastros extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExecutaCadastros';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa todos os processos de cadastros em sequência';

    public function handle()
    {
        $key = time();
        Log::info("{$key} - Início " . static::class);

        $commands = [
            Intermediary\ProcessaIntermediario::class,
            function() {
                $this->call('nexti-sync', ['folder' => 'schedule-transfer']);
            },
            function() {
                $this->call('nexti-sync', ['folder' => 'workplace-transfer']);
            },
            Intermediary\ProcessaIntermediario::class,
            Nexti\ProcessaEmpresa::class,
            Nexti\ProcessaCargo::class,
            // Nexti\ProcessaCliente::class,
            // Nexti\Get\BuscaPosto::class,
            // Intermediary\Merge\MergePosto::class,
            // Nexti\ProcessaPosto::class,
            Nexti\ProcessaSindicato::class,
            Nexti\ProcessaSituacao::class,
            Nexti\ProcessaColaborador::class,
            // Nexti\ProcessaTrocaPosto::class,
            // Nexti\Get\BuscaTrocaPosto::class,
            // Intermediary\Merge\MergeTrocaPosto::class,
            // Nexti\ProcessaTrocaEscala::class,
            // Nexti\Get\BuscaTrocaEscala::class,
            // Intermediary\Merge\MergeTrocaEscala::class,

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
