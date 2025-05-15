<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Career;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;

class InsertCargo extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertCargo';

    /**
     * Dispatch Item
     * 
     * @param object $cargo
     * @return string
     **/
    protected function dispachItem(object $cargo): string
    {
		$campos = [
			'CDCHAMADA' => $cargo->cdchamada,
			'ESTCAR' => 1,
			'CODCAR' => $cargo->cdchamada,
			'TITCAR' => trim($cargo->nmfuncao),
			'IDEXTERNO' => $cargo->cdchamada,
		];

		$found = Career::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Career::insert($campos);

			return "Cargo {$campos['IDEXTERNO']} Criada!";
		}

		if($found->ID && $this->hasDiff($found->toArray(), $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Cargo {$campos['IDEXTERNO']} Atualizada!";
    }

    /**
     * Busca Cargos no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
    	return DBPG::initialize()
    				->table('wdp.funcoesb')
    				->get();
    }
}