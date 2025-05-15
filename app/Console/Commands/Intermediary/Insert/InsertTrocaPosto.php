<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Query\JoinClause;
use App\Models\Workplace\WorkplaceTransfer;
use App\Shared\Commands\CommandIntermediary;

class InsertTrocaPosto extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertTrocaPosto';

    /**
     * Dispatch Item
     * 
     * @param object $troca
     * @return string
     **/
    protected function dispachItem(object $troca): string
    {
		$campos = [
			'IDTROCADPTO' => $troca->idtrocadpto,
			'NUMEMP'      => $troca->numemp,
			'TIPCOL'      => 1,
			'NUMCAD'      => $troca->matricula_esocial,
			'INIATU'      => Date::parse($troca->dtmudanca)->format('Y-m-d'),
			'SEQHIS'      => 1,
			'POSTO'       => $troca->deptoidempresa . '-' . $troca->deptocdchamada,
			'IDEXTERNO'   => $troca->idtrocadpto,
			'TABORG'      => 0,
			'NUMLOC'      => 0,
			'CODLOC'      => 0,
			'CODCCU'      => 0,
		];

		$found = WorkplaceTransfer::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			WorkplaceTransfer::create($campos);

			return "Troca de Posto {$campos['IDEXTERNO']} criado!";
		}

		$toArray = array_merge($found->toArray(), [
			'INIATU' => $found->INIATU?->format('Y-m-d')
		]);

		if($found->ID && $this->hasDiff($toArray, $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Troca de Posto {$campos['IDEXTERNO']} atualizado!";
    }

    /**
     * Busca trocas de posto no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
        return DBPG::initialize()
        	->table('wdp.depto')
            ->select([
                'postos.idtrocadpto',
                'postos.matricula_esocial',
                'postos.dtmudanca',
                'postos.numemp',
            ])
            ->selectRaw('wdp.depto.cdchamada AS deptocdchamada') 
            ->selectRaw('wdp.depto.idempresa AS deptoidempresa')
            ->joinSub(
            	query: fn(Builder $db) => $db->from('wdp.t00001')
				                    ->select([
				                        'wdp.t00001.idtrocadpto',
				                        'wdp.f00001.matricula_esocial',
				                        'wdp.t00001.dtmudanca',
				                        'wdp.t00001.idsetoratual',
				                    ])
				                    ->selectRaw("'1' as numemp")
				                    ->join('wdp.f00001',
				                    	'wdp.f00001.idfuncionario', '=', 'wdp.t00001.idfuncionario'
				                    )
				                    ->where('wdp.f00001.dtdemissao')
				                    ->union(
					                    fn(Builder $union) =>
						                    $union->from('wdp.t00002')
							                    ->select([
							                        'wdp.t00002.idtrocadpto',
							                        'wdp.f00002.matricula_esocial',
							                        'wdp.t00002.dtmudanca',
							                        'wdp.t00002.idsetoratual',
							                    ])
							                    ->selectRaw("'2' as numemp")
							                    ->join('wdp.f00002',
							                    	'wdp.f00002.idfuncionario', '=', 'wdp.t00002.idfuncionario'
							                    )
							                    ->where('wdp.f00002.dtdemissao')
					                )
				                    ->union(
					                    fn(Builder $union) =>
						                    $union->from('wdp.t00003')
							                    ->select([
							                        'wdp.t00003.idtrocadpto',
							                        'wdp.f00003.matricula_esocial',
							                        'wdp.t00003.dtmudanca',
							                        'wdp.t00003.idsetoratual',
							                    ])
							                    ->selectRaw("'3' as numemp")
							                    ->join('wdp.f00003', 
							                    	'wdp.f00003.idfuncionario', '=', 'wdp.t00003.idfuncionario'
							                    )
							                    ->where('wdp.f00003.dtdemissao')
					                ),
				as: 'postos',
				first: fn(JoinClause $join) => $join->on('wdp.depto.iddepartamento', '=', 'postos.idsetoratual')
            )
            ->where('wdp.depto.sttipo', 'A')
            ->get();
    }
}