<?php

namespace App\Console\Commands\Intermediary\Insert\ContraCheque;

use App\Shared\DBPG;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\Paycheck\Competence;
use App\Shared\Commands\CommandIntermediary;
use App\Console\Commands\Intermediary\Shared\IdExternoContraCheque;

class InsertContraChequeCompetence extends CommandIntermediary
{
	use IdExternoContraCheque;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'InsertContraChequeCompetence';

    /**
     * Dispatch Item
     **/
    protected function dispachItem(object $competencia): string
    {
    	$competencia->dtpagamento = $competencia->dtpagamento ? Carbon::parse($competencia->dtpagamento) : null;

		$campos = [
			'NUMEMP' => $competencia->numemp,
	        'NAME' => $competencia->dtpagamento?->format('m/Y') . ' - ' . trim($competencia->dshistorico),
	        'PAYCHECKPERIODDATE' => $competencia->dtpagamento?->format('Y-m-01'),
	        'DATPAG' => $competencia->dtpagamento?->format('Y-m-d'),
		];

		if(!$campos['NAME'] || !$campos['PAYCHECKPERIODDATE'] || !$campos['DATPAG']) {
			return 'Campos faltantes';
		}

		$campos['IDEXTERNO'] = $this->generateIdExterno($competencia);

		$found = Competence::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Competence::create($campos);

			return "Competencia {$campos['IDEXTERNO']} criado!";
		}

		$toArray = array_merge($found->toArray(), [
			'PAYCHECKPERIODDATE' => $found->PAYCHECKPERIODDATE?->format('Y-m-d'),
			'DATPAG' => $found->DATPAG?->format('Y-m-d')
		]);

		if($found->ID && $this->hasDiff($toArray, $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Competencia {$campos['IDEXTERNO']} atualizado!";
    }

    /**
     * Busca competencias
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistros(): Collection
    {
		return collect()
				->merge($this->buscaMensal())
				->merge($this->buscaPrimeiraDecimo())
				->merge($this->buscaFerias());
    }

	/**
	 * Busca competencias mensal
	 **/
	private function buscaMensal(): array
	{
    	$query = "
			SELECT
				f00001.idfuncionario,
				r00001.dtpagamento,
				r00001.tpprocesso,
				f00001.cdchamada,
				h00001.dshistorico,
				1 as numemp
			FROM wdp.r00001
			INNER JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			INNER JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND TO_CHAR(r00001.dtpagamento + '1month', 'yyyy-mm-07')::DATE <= CURRENT_DATE
			AND f00001.dtdemissao IS null
			AND r00001.tpprocesso = 'M'
			GROUP BY f00001.idfuncionario, r00001.dtpagamento, r00001.tpprocesso, h00001.idhistorico

			UNION

			SELECT
				f00002.idfuncionario,
				r00002.dtpagamento,
				r00002.tpprocesso,
				f00002.cdchamada,
				h00002.dshistorico,
				2 as numemp
			FROM wdp.r00002
			INNER JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			INNER JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			WHERE r00002.dtpagamento::DATE >= '2024-01-01'
			AND TO_CHAR(r00002.dtpagamento + '1month', 'yyyy-mm-07')::DATE <= CURRENT_DATE
			AND f00002.dtdemissao IS null
			AND r00002.tpprocesso = 'M'
			GROUP BY f00002.idfuncionario, r00002.dtpagamento, r00002.tpprocesso, h00002.idhistorico

			UNION

			SELECT
				f00003.idfuncionario,
				r00003.dtpagamento,
				r00003.tpprocesso,
				f00003.cdchamada,
				h00003.dshistorico,
				3 as numemp
			FROM wdp.r00003
			INNER JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			INNER JOIN wdp.f00003
				ON(f00003.idfuncionario = r00003.idfuncionario)
			WHERE r00003.dtpagamento::DATE >= '2024-01-01'
			AND TO_CHAR(r00003.dtpagamento + '1month', 'yyyy-mm-07')::DATE <= CURRENT_DATE
			AND f00003.dtdemissao IS null
			AND r00003.tpprocesso = 'M'
			GROUP BY f00003.idfuncionario, r00003.dtpagamento, r00003.tpprocesso, h00003.idhistorico
    	";

		return DBPG::initialize()->select($query);
	}
	
	/**
	 * Busca competencias primeira decimo
	 **/
	private function buscaPrimeiraDecimo(): array
	{
    	$query = "
			SELECT
				f00001.idfuncionario,
				r00001.dtpagamento,
				r00001.tpprocesso,
				f00001.cdchamada,
				h00001.dshistorico,
				1 as numemp
			FROM wdp.r00001
			INNER JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			INNER JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND r00001.dtpagamento::DATE <= CURRENT_DATE
			AND f00001.dtdemissao IS null
			AND r00001.tpprocesso = '1'
			GROUP BY f00001.idfuncionario, r00001.dtpagamento, r00001.tpprocesso, h00001.idhistorico

			UNION ALL

			SELECT
				f00002.idfuncionario,
				r00002.dtpagamento,
				r00002.tpprocesso,
				f00002.cdchamada,
				h00002.dshistorico,
				2 as numemp
			FROM wdp.r00002
			INNER JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			INNER JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			WHERE r00002.dtpagamento::DATE >= '2024-01-01'
			AND r00002.dtpagamento::DATE <= CURRENT_DATE
			AND f00002.dtdemissao IS null
			AND r00002.tpprocesso = '1'
			GROUP BY f00002.idfuncionario, r00002.dtpagamento, r00002.tpprocesso, h00002.idhistorico

			UNION ALL

			SELECT
				f00003.idfuncionario,
				r00003.dtpagamento,
				r00003.tpprocesso,
				f00003.cdchamada,
				h00003.dshistorico,
				3 as numemp
			FROM wdp.r00003
			INNER JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			INNER JOIN wdp.f00003
				ON(f00003.idfuncionario = r00003.idfuncionario)
			WHERE r00003.dtpagamento::DATE >= '2024-01-01'
			AND r00003.dtpagamento::DATE <= CURRENT_DATE
			AND f00003.dtdemissao IS null
			AND r00003.tpprocesso = '1'
			GROUP BY f00003.idfuncionario, r00003.dtpagamento, r00003.tpprocesso, h00003.idhistorico
    	";

		return DBPG::initialize()->select($query);
	}

    /**
     * Busca competencias de ferias
     **/
    private function buscaFerias(): array
    {
    	$query = "
			SELECT
				f00001.idfuncionario,
				r00001.dtpagamento,
				r00001.tpprocesso,
				f00001.cdchamada,
				h00001.dshistorico,
				1 as numemp
			FROM wdp.r00001
			INNER JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			INNER JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND r00001.dtpagamento::DATE <= CURRENT_DATE
			AND f00001.dtdemissao IS null
			AND r00001.tpprocesso = 'F'
			GROUP BY f00001.idfuncionario, r00001.dtpagamento, r00001.tpprocesso, h00001.idhistorico

			UNION

			SELECT
				f00002.idfuncionario,
				r00002.dtpagamento,
				r00002.tpprocesso,
				f00002.cdchamada,
				h00002.dshistorico,
				2 as numemp
			FROM wdp.r00002
			INNER JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			INNER JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			WHERE r00002.dtpagamento::DATE >= '2024-01-01'
			AND r00002.dtpagamento::DATE <= CURRENT_DATE
			AND f00002.dtdemissao IS null
			AND r00002.tpprocesso = 'F'
			GROUP BY f00002.idfuncionario, r00002.dtpagamento, r00002.tpprocesso, h00002.idhistorico

			UNION

			SELECT
				f00003.idfuncionario,
				r00003.dtpagamento,
				r00003.tpprocesso,
				f00003.cdchamada,
				h00003.dshistorico,
				3 as numemp
			FROM wdp.r00003
			INNER JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			INNER JOIN wdp.f00003
				ON(f00003.idfuncionario = r00003.idfuncionario)
			WHERE r00003.dtpagamento::DATE >= '2024-01-01'
			AND r00003.dtpagamento::DATE <= CURRENT_DATE
			AND f00003.dtdemissao IS null
			AND r00003.tpprocesso = 'F'
			GROUP BY f00003.idfuncionario, r00003.dtpagamento, r00003.tpprocesso, h00003.idhistorico
    	";

		return DBPG::initialize()->select($query);
    }
}