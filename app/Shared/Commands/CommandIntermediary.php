<?php

namespace App\Shared\Commands;

use Illuminate\Support\Collection;

abstract class CommandIntermediary extends Command
{
    /**
     * Log message string info
     **/
    protected bool $log = true;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->generateKey();

    	$registros = $this->buscaRegistros();

		$this->info("Total de {$registros->count()} Registros!");

        foreach($registros->lazy() as $item) {
            $message = $this->dispachItem($item);
            if($this->log) {
                $this->info($message);
            }
        }

        $this->afterExecute();

        return static::SUCCESS;
    }

    /**
     * Busca Data no banco do cliente
     **/
    protected abstract function buscaRegistros(): Collection;

    /**
     * Dispatch Item
     **/
    protected abstract function dispachItem(object $item): string;

    /**
     * After execute
     */
    protected function afterExecute(): void
    {
        //
    }

    /**
     * Check data Diff
     */
    protected function hasDiff(array $attributes, array $values): bool
    {
        $hasDiff = false;

        foreach ($values as $key => $value) {
            if(!array_key_exists($key, $attributes)) {
                continue;
            }

            if($attributes[$key] != $value) {
                $hasDiff = true;
                break;
            }
        }

        return $hasDiff;
    }
}