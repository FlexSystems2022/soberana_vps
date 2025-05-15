<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Workplace;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use App\Shared\Commands\CommandIntermediary;

class InsertPosto extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertPosto';

    /**
     * Dispatch Item
     * 
     * @param object $posto
     * @return string
     **/
    protected function dispachItem(object $posto): string
    {
		$campos = [
			'CDCHAMADA' => $posto->cdchamada,
			'ESTPOS' => 1,
			'POSTRA' => $posto->cdchamada,
			'DESPOS' => $posto->cdchamada . ' - ' . $posto->nmdepartamento,
			'SERVICO' => 'Padrão',
			'VAGAS' => 0,
			'DATCRI' => '1990-12-31',
			'CODOEM' => $posto->codoem,
			'UNIDADE_NEGOCIO' => config('nexti.workplace.business_unit_id'),
			'TIPO_SERVICO' => config('nexti.workplace.service_type_id'),
			'CODFIL' => 1,
			'NUMEMP' => $posto->idempresa,
			'DATEXT' => $posto->data_desativacao,
			'IDEXTERNO' => $posto->idempresa . '-' . $posto->cdchamada,
			'CPFCGC' => $posto->nrcgc
		];

		$found = Workplace::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Workplace::create($campos);

			return "Posto {$campos['IDEXTERNO']} Criado!";
		}

		$toArray = array_merge($found->toArray(), [
			'DATCRI' => $found->DATCRI?->format('Y-m-d'),
			'DATEXT' => $found->DATEXT?->format('Y-m-d')
		]);

		if($found->ID && $this->hasDiff($toArray, $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Posto {$campos['IDEXTERNO']} Atualizado!";
    }

    /**
     * Busca postos no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
    	return DBPG::initialize()
    				->table('wdp.depto')
    				->select('wdp.depto.*')
    				->selectRaw('cliente.cdchamada AS codoem')
    				->join(new Expression('wdp.depto AS cliente'),
    					fn(JoinClause $join) => $join->on(new Expression("split_part(cliente.cdclassificacao, '.', 1)"), '=', new Expression("split_part(wdp.depto.cdclassificacao, '.', 1)"))
	    					->where('cliente.sttipo', 'S')
    				)
    				->where('wdp.depto.sttipo', 'A')
    				->get();
    }
}