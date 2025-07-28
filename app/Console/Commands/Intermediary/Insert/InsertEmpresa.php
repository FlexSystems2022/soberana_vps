<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Company;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;

class InsertEmpresa extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertEmpresa';

    /**
     * Dispatch Item
     * 
     * @param object $empresa
     * @return string
     **/
    protected function dispachItem(object $empresa): string
    {
		$campos = [
			'CDEMPRESA' => $empresa->cdempresa,
			'NUMEMP' => $empresa->cdempresa,
			'CODFIL' => 1,
			'RAZSOC' => $empresa->nmempresa,
			'NOMFIL' => $empresa->nmfantasia ?: $empresa->nmempresa,
			'CNPJ' => preg_replace('/[^0-9]/', '', trim($empresa->nrcgc)),
			'IDEXTERNO' => $empresa->cdempresa,
		];

		$found = Company::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Company::create($campos);

			return "Empresa {$campos['IDEXTERNO']} Criada!";
		}

		if($found->ID && $this->hasDiff($found->toArray(), $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Empresa {$campos['IDEXTERNO']} Atualizada!";
    }

    /**
     * Busca empresas no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
    	return DBPG::initialize()
    				->table('wphd.empresa')
    				->get();
    }
}