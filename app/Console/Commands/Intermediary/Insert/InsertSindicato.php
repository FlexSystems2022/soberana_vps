<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Union;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;

class InsertSindicato extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertSindicato';

    /**
     * Dispatch Item
     * 
     * @param object $sindicato
     * @return string
     **/
    protected function dispachItem(object $sindicato): string
    {
		$campos = [
			'CDCHAMADA' => $sindicato->cdchamada,
			'CODSIN' => $sindicato->cdchamada,
			'NOMSIN' => $sindicato->nmsindicato,
			'IDEXTERNO' => $sindicato->cdchamada,
		];

		$found = Union::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Union::create($campos);

			return "Sindicato {$campos['IDEXTERNO']} Criada!";
		}

		if($found->ID && $this->hasDiff($found->toArray(), $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Sindicato {$campos['IDEXTERNO']} Atualizada!";
    }

    /**
     * Busca Sindicatos no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
    	return DBPG::initialize()
    				->table('wdp.sind')
    				->get();
    }
}