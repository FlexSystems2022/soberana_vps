<?php

namespace App\Console\Commands\Intermediary;

use Illuminate\Console\Command;

class ProcessaIntermediario extends Command
{
    /**
     * @var bool
     */
    protected bool $log = true;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaIntermediario';

    /**
     * Execute the console command.
     * 
     * @return int
     **/
    public function handle(): int
    {
    	$commands = [
            Insert\InsertSituacao::class,
			Insert\InsertSindicato::class,
			Insert\InsertCargo::class,
			Insert\InsertEmpresa::class,
			Insert\InsertCliente::class,
            Insert\InsertPosto::class,
            Insert\InsertColaborador::class,
            Insert\InsertTrocaPosto::class,
		];

        foreach($commands as $class) {
            $command = $this->resolveCommand($class);

            $this->info("Executando [{$command->getName()}]");

            $this->call($class);
        }

		return static::SUCCESS;
    }
}