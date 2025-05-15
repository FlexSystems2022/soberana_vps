<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\People;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;

class InsertColaborador extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertColaborador';

    /**
     * Dispatch Item
     * 
     * @param object $colaborador
     * @return string
     **/
    protected function dispachItem(object $colaborador): string
    {
		$fields = [
			'CDCHAMADA' => $colaborador->cdchamada,
			'NUMEMP' => $colaborador->numemp,
			'TIPCOL' => 1,
			'NUMCAD' => $colaborador->matricula_esocial,
			'NOMFUN' => trim($colaborador->nmfuncionario),
			'DATANASC' => $colaborador->dtnascimento ?? null,
			'DATADM' => $colaborador->dtadmissao ?? null,
			'DATADEM' => $colaborador->dtdemissao ?? null,
			'CODFIL' => 1,
			'CARGO' => $colaborador->idexternocargo,
			'ESCALA' => null,
			'POSTO' => $colaborador->posto,
			'SITFUN' => 1,
			'IDEXTERNO' => $colaborador->numemp . '-' . $colaborador->cdchamada,
			'TABORG' => null,
			'NUMLOC' => null,
			'TELEFONE' => preg_replace('/[^0-9]/', '', trim($colaborador->nrtelefone)) ?: null,
			'CELULAR' => preg_replace('/[^0-9]/', '', trim($colaborador->nrcelular)) ?: null,
			'ENDERECO' => trim($colaborador->nmendereco) ?: null,
			'NUMERO' => trim($colaborador->nrendereco) ?: null,
			'BAIRRO' => trim($colaborador->nmbairro) ?: null,
			'CPF' => preg_replace('/[^0-9]/', '', trim($colaborador->nrcpf)) ?: null,
			'PIS' => trim($colaborador->nrpispasep) ?: null,
			'EMAIL' => null,
		];

		if($fields['DATADEM']) {
			$fields['SITFUN'] = 3;
		}

		if($fields['DATANASC']) {
			$fields['DATANASC'] = Carbon::parse($fields['DATANASC'])->format('Y-m-d');
		}

		if($fields['DATADM']) {
			$fields['DATADM'] = Carbon::parse($fields['DATADM'])->format('Y-m-d');
		}

		if($fields['DATADEM']) {
			$fields['DATADEM'] = Carbon::parse($fields['DATADEM'])->format('Y-m-d');
		}

		$found = People::where('IDEXTERNO', $fields['IDEXTERNO'])->first();
		if(!$found) {
			if($fields['DATADEM']) {
				return "Colaborador {$fields['IDEXTERNO']} ignorado!";
			}

			People::create($fields);

			return "Colaborador {$fields['IDEXTERNO']} criado!";
		}

		$toArray = array_merge($found->toArray(), [
			'DATANASC' => $found->DATANASC?->format('Y-m-d'),
			'DATADM' => $found->DATADM?->format('Y-m-d'),
			'DATADEM' => $found->DATADEM?->format('Y-m-d'),
		]);

		if($found->ID && $this->hasDiff($toArray, $fields)) {
			if($found->TIPO->value != 3) {
				$fields['TIPO'] = 1;
				$fields['SITUACAO'] = 0;
			}
		}

		$found->update($fields);

		return "Colaborador {$fields['IDEXTERNO']} atualizado!";
    }

    /**
     * Busca colaboradores no banco do cliente
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
    	$empresa_1 =  DBPG::initialize()
	    				->table('wdp.f00001')
	    				->select([
							'wdp.f00001.cdchamada',
							'wdp.f00001.matricula_esocial',
							'wdp.f00001.nmfuncionario',
							'wdp.f00001.dtnascimento',
							'wdp.f00001.dtadmissao',
							'wdp.f00001.cdchamada',
							'wdp.f00001.nrtelefone',
							'wdp.f00001.nrcelular',
							'wdp.f00001.nmendereco',
							'wdp.f00001.nrendereco',
							'wdp.f00001.nmbairro',
							'wdp.f00001.nrcpf',
							'wdp.f00001.dtdemissao',
							'wdp.f00001.nrpispasep'
	    				])
	    				->selectRaw('1 as numemp')
	    				->selectRaw('wdp.depto.cdchamada as posto')
	    				->selectRaw('wdp.funcoesb.cdchamada as idexternocargo')
	    				->leftJoin('wdp.funcoesb',
	    					'wdp.funcoesb.idfuncao', '=', 'wdp.f00001.idfuncao'
	    				)
	    				->leftJoin('wdp.depto',
	    					'wdp.depto.iddepartamento', '=', 'wdp.f00001.iddepartamento'
	    				);

    	$empresa_2 =  DBPG::initialize()
	    				->table('wdp.f00002')
	    				->select([
							'wdp.f00002.cdchamada',
							'wdp.f00002.matricula_esocial',
							'wdp.f00002.nmfuncionario',
							'wdp.f00002.dtnascimento',
							'wdp.f00002.dtadmissao',
							'wdp.f00002.cdchamada',
							'wdp.f00002.nrtelefone',
							'wdp.f00002.nrcelular',
							'wdp.f00002.nmendereco',
							'wdp.f00002.nrendereco',
							'wdp.f00002.nmbairro',
							'wdp.f00002.nrcpf',
							'wdp.f00002.dtdemissao',
							'wdp.f00002.nrpispasep'
	    				])
	    				->selectRaw('2 as numemp')
	    				->selectRaw('wdp.depto.cdchamada as posto')
	    				->selectRaw('wdp.funcoesb.cdchamada as idexternocargo')
	    				->leftJoin('wdp.funcoesb',
	    					'wdp.funcoesb.idfuncao', '=', 'wdp.f00002.idfuncao'
	    				)
	    				->leftJoin('wdp.depto',
	    					'wdp.depto.iddepartamento', '=', 'wdp.f00002.iddepartamento'
	    				);

    	return DBPG::initialize()
    				->table('wdp.f00003')
    				->select([
						'wdp.f00003.cdchamada',
						'wdp.f00003.matricula_esocial',
						'wdp.f00003.nmfuncionario',
						'wdp.f00003.dtnascimento',
						'wdp.f00003.dtadmissao',
						'wdp.f00003.cdchamada',
						'wdp.f00003.nrtelefone',
						'wdp.f00003.nrcelular',
						'wdp.f00003.nmendereco',
						'wdp.f00003.nrendereco',
						'wdp.f00003.nmbairro',
						'wdp.f00003.nrcpf',
						'wdp.f00003.dtdemissao',
						'wdp.f00003.nrpispasep'
    				])
    				->selectRaw('3 as numemp')
					->selectRaw('wdp.depto.cdchamada as posto')
    				->selectRaw('wdp.funcoesb.cdchamada as idexternocargo')
    				->leftJoin('wdp.funcoesb',
    					'wdp.funcoesb.idfuncao', '=', 'wdp.f00003.idfuncao'
    				)
    				->leftJoin('wdp.depto',
    					'wdp.depto.iddepartamento', '=', 'wdp.f00003.iddepartamento'
    				)
    				->union($empresa_1)
    				->union($empresa_2)
    				->get();
    }
}