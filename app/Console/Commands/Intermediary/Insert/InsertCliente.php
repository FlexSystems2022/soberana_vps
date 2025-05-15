<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Client;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;

class InsertCliente extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertCliente';

    /**
     * Dispatch Item
     * 
     * @param object $cliente
     * @return string
     **/
    protected function dispachItem(object $cliente): string
    {
		$campos = [
			'CDCHAMADA' => $cliente->cdchamada,
			'NUMEMP' => $cliente->idempresa,
			'CODOEM' => $cliente->cdchamada,
			'NOMOEM' => $cliente->cdchamada . ' - ' . $cliente->nmdepartamento,
			'IDEXTERNO' => $cliente->cdchamada,
		];

		$found = Client::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Client::create($campos);

			return "Cliente {$campos['IDEXTERNO']} Criada!";
		}

		if($found->ID && $this->hasDiff($found->toArray(), $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Cliente {$campos['IDEXTERNO']} Atualizada!";
    }

    /**
     * Busca clientes no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
    	return DBPG::initialize()
    				->table('wdp.depto')
    				->where('wdp.depto.sttipo', 'S')
    				->get();
    }
}