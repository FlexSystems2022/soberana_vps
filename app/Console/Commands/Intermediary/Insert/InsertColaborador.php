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
    // Ignorar se não houver matrícula
    if (empty($colaborador->matricula_esocial)) {
        return "Colaborador {$colaborador->numemp}-{$colaborador->cdchamada} ignorado (sem matrícula_esocial)!";
    }

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
        'TIPO' => 0,
        'SITUACAO' => 0,
        'OBSERVACAO' => '',
        'ID' => 0,
        'IDEXTERNO' => $colaborador->numemp . '-' . $colaborador->cdchamada,
        'TABORG' => null,
        'NUMLOC' => null,
        'IGNOREVALIDATION' => 0,
        'TELEFONE' => preg_replace('/[^0-9]/', '', trim($colaborador->nrtelefone)) ?: null,
        'CELULAR' => preg_replace('/[^0-9]/', '', trim($colaborador->nrcelular)) ?: null,
        'ENDERECO' => trim($colaborador->nmendereco) ?: null,
        'NUMERO' => trim($colaborador->nrendereco) ?: null,
        'BAIRRO' => trim($colaborador->nmbairro) ?: null,
        'CPF' => preg_replace('/[^0-9]/', '', trim($colaborador->nrcpf)) ?: null,
        'PIS' => trim($colaborador->nrpispasep) ?: null,
        'EMAIL' => null,
    ];

    if ($fields['DATADEM']) {
        $fields['SITFUN'] = 3;
    }

    if ($fields['DATANASC']) {
        $fields['DATANASC'] = Carbon::parse($fields['DATANASC'])->format('Y-m-d');
    }

    if ($fields['DATADM']) {
        $fields['DATADM'] = Carbon::parse($fields['DATADM'])->format('Y-m-d');
    }

    if ($fields['DATADEM']) {
        $fields['DATADEM'] = Carbon::parse($fields['DATADEM'])->format('Y-m-d');
    }

    $found = People::where('IDEXTERNO', $fields['IDEXTERNO'])->first();
    if (!$found) {
        if ($fields['DATADEM']) {
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

    if ($found->ID && $this->hasDiff($toArray, $fields)) {
        if ($found->TIPO->value != 3) {
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
		$tabelas = [
			'f00001', 'f00002', 'f00003', 'f00004', 'f00005', 'f00006', 'f00007', 'f00008', 'f00009',
			'f00014', 'f00017', 'f00020', 'f01000', 'f01001', 'f01002', 'f01003', 'f01004', 'f01005',
			'f01006', 'f01007', 'f01008', 'f01009', 'f01010', 'f01011', 'f02021', 'f02022', 'f02023',
			'f02024', 'f02025', 'f02026', 'f02027', 'f02028', 'f02029', 'f02030', 'f02031', 'f02032',
			'f02033', 'f02034', 'f02035', 'f02036', 'f02037', 'f02038', 'f02039', 'f02040', 'f02041',
			'f02042', 'f02043', 'f02044', 'f02045', 'f02046', 'f02047'
		];

		$queries = [];
		foreach ($tabelas as $index => $tabela) {
			$query = DBPG::initialize()
				->table("wdp.$tabela")
				->select([
					"wdp.$tabela.cdchamada",
					"wdp.$tabela.matricula_esocial",
					"wdp.$tabela.nmfuncionario",
					"wdp.$tabela.dtnascimento",
					"wdp.$tabela.dtadmissao",
					"wdp.$tabela.nrtelefone",
					"wdp.$tabela.nrcelular",
					"wdp.$tabela.nmendereco",
					"wdp.$tabela.nrendereco",
					"wdp.$tabela.nmbairro",
					"wdp.$tabela.nrcpf",
					"wdp.$tabela.dtdemissao",
					"wdp.$tabela.nrpispasep",
				])
				->selectRaw('? as numemp', [$index + 1])
				->selectRaw('wdp.depto.cdchamada as posto')
				->selectRaw('wdp.funcoesb.cdchamada as idexternocargo')
				->leftJoin('wdp.funcoesb', "wdp.funcoesb.idfuncao", "=", "wdp.$tabela.idfuncao")
				->leftJoin('wdp.depto', "wdp.depto.iddepartamento", "=", "wdp.$tabela.iddepartamento");

			$queries[] = $query;
		}

		// Combina todos os selects usando union
		$mainQuery = array_shift($queries);
		foreach ($queries as $q) {
			$mainQuery->union($q);
		}

		return $mainQuery->get();
	}
}