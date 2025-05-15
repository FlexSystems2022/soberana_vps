<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Shift;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;

class InsertHorario extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertHorario';

    /**
     * Dispatch Item
     * 
     * @param object $horario
     * @return string
     **/
    protected function dispachItem(object $horario): string
    {
		$campos = [
			'CODHOR' => $horario->cdchamada,
			'DESHOR' => trim($horario->dshorario),
			'ACTIVE'=> 1,
			'SHIFTTYPEID' => 5, // MOVEL REFEIÇÃO
			'IDEXTERNO' => $horario->cdchamada
		];

		$mapped = $this->mapHorario($horario);

		$campos['ENTRADA1'] = $mapped['ENTRADA1'];
		$campos['SAIDA1'] = $mapped['SAIDA1'];
		$campos['ENTRADA2'] = $mapped['ENTRADA2'];
		$campos['SAIDA2'] = $mapped['SAIDA2'];

		$found = Shift::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Shift::create($campos);

			return "Horario {$campos['IDEXTERNO']} Criado!";
		}

		if($found->ID && $this->hasDiff($found->toArray(), $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Horario {$campos['IDEXTERNO']} Atualizado!";
    }

    /**
     * Busca Cargos no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
    	return DBPG::initialize()
    				->table('wdp.horario')
    				->get();
    }

    /**
     * Map Horario
     * 
     * @param object $horario
     * 
     * @return array
     **/
    private function mapHorario(object $horario): array
    {
    	$map = function(string|null $hour): string|null {
    		if(!$hour) {
    			return null;
    		}

    		$hour = str($hour)->trim();
    		if($hour->length() === 0) {
    			return null;
    		}

    		return $hour->substr(0, 2)->padLeft(2, '0') . ':' . $hour->substr(2)->padLeft(2, '0') . ':00';
    	};

    	$dates = [
    		'ENTRADA1' => $map($horario->hrentrada),
    		'SAIDA1' => $map($horario->hrsaidaalmoco),
    		'ENTRADA2' => $map($horario->hrvoltaalmoco),
    		'SAIDA2' => $map($horario->hrsaida),
    	];

    	if(!$horario->dsintervalolanche) {
    		return $dates;
    	}

    	$hours_1 = str($horario->dsintervalolanche)->substr(0, 4)->toString();
    	$hours_2 = str($horario->dsintervalolanche)->substr(4, 4)->toString();

    	$dates['SAIDA1'] = $map($hours_1);
    	$dates['ENTRADA2'] = $map($hours_2);

    	return $dates;
    }
}