<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Absence\Situation;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;

class InsertSituacao extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertSituacao';

    /**
     * Dispatch Item
     * 
     * @param object $situacao
     * @return string
     **/
    protected function dispachItem(object $situacao): string
    {
		$campos = [
			'IDESPECIAL' => $situacao->idespecial,
			'CODSIT' => $situacao->cdchamada,
			'DESSIT' => $situacao->nmespecial,
			'IDEXTERNO' => $situacao->cdchamada,
		];

		$found = Situation::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Situation::create($campos);

			return "Situação {$campos['IDEXTERNO']} Criada!";
		}

		if($found->ID && $this->hasDiff($found->toArray(), $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Situação {$campos['IDEXTERNO']} Atualizada!";
    }

    /**
     * Busca Situações no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
    	return DBPG::initialize()
    				->table('wdp.especial')
    				->get();
    }
}