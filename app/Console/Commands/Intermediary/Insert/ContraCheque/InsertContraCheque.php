<?php

namespace App\Console\Commands\Intermediary\Insert\ContraCheque;

use App\Shared\DBPG;
use App\Models\Paycheck;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;
use App\Console\Commands\Intermediary\Shared\IdExternoContraCheque;

class InsertContraCheque extends CommandIntermediary
{
	use IdExternoContraCheque;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'InsertContraCheque';

    /**
     * Dispatch Item
     **/
    protected function dispachItem(object $contracheque): string
    {
    	$contracheque->dtpagamento = $contracheque->dtpagamento ? Carbon::parse($contracheque->dtpagamento) : null;

		$campos = [
			'NUMEMP' => $contracheque->numemp,
			'TIPCOL' => 1,
			'NUMCAD' => $contracheque->matricula_esocial,
			'BASEFGTS' => $contracheque->basefgts ?? 0,
			'MONTHFGTS' => $contracheque->monthfgts ?? 0,
			'BASEINSS' => $contracheque->baseinss ?? 0,
			'GROSSPAY' => $contracheque->grosspay ?? 0,
			'IDEXTERNO' => $this->generateIdExterno($contracheque)
		];

		$campos['CONTRA_CHEQUE_CMP'] = $campos['IDEXTERNO'];

		$found = Paycheck::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if(!$found) {
			Paycheck::create($campos);

			return "Contra Cheque {$campos['IDEXTERNO']} criado!";
		}

		if($found->ID && $this->hasDiff($found->toArray(), $campos)) {
			if($found->TIPO->value != 3) {
				$campos['TIPO'] = 1;
				$campos['SITUACAO'] = 0;
			}
		}

		$found->update($campos);

		return "Contra Cheque {$campos['IDEXTERNO']} atualizado!";
    }

    /**
     * Busca contra cheques no banco do cliente
     **/
    protected function buscaRegistros(): Collection
    {
		return collect()
				->merge($this->buscaMensal())
				->merge($this->buscaPrimeiraDecimo())
				->merge($this->buscaFerias());
	}

	/**
	 * Busca contra cheque mensal
	 **/
	private function buscaMensal(): array
	{
    	$query = "
			SELECT
				f00001.idfuncionario,
				f00001.cdchamada,
				f00001.matricula_esocial,
				r00001.tpprocesso,
				r00001.dtpagamento,
				h00001.dshistorico,
				1 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00001 AS evt
					WHERE evt.idhistorico = h00001.idhistorico
					AND evt.idfuncionario = f00001.idfuncionario
					AND evt.sttipoevento = 'FG'
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00001 AS evt
					WHERE evt.idhistorico = h00001.idhistorico
					AND evt.idfuncionario = f00001.idfuncionario
					AND evt.sttipoevento = 'I'
				) AS baseinss,
				(
					SELECT
						SUM(evt.vlevento)
					FROM wdp.r00001 AS evt
					WHERE evt.idhistorico = h00001.idhistorico
					AND evt.idfuncionario = f00001.idfuncionario
					AND evt.idevento IN('1/326', '001000001I')
				) AS grosspay
			FROM wdp.r00001
			JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND TO_CHAR(r00001.dtpagamento + '1month', 'yyyy-mm-07')::DATE <= CURRENT_DATE
			AND f00001.dtdemissao IS null
			AND r00001.tpprocesso = 'M'
			GROUP BY f00001.idfuncionario, r00001.dtpagamento, r00001.tpprocesso, h00001.idhistorico

			UNION

			SELECT
				f00002.idfuncionario,
				f00002.cdchamada,
				f00002.matricula_esocial,
				r00002.tpprocesso,
				r00002.dtpagamento,
				h00002.dshistorico,
				2 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00002 AS evt
					WHERE evt.idhistorico = h00002.idhistorico
					AND evt.idfuncionario = f00002.idfuncionario
					AND evt.sttipoevento = 'FG'
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00002 AS evt
					WHERE evt.idhistorico = h00002.idhistorico
					AND evt.idfuncionario = f00002.idfuncionario
					AND evt.sttipoevento = 'I'
				) AS baseinss,
				(
					SELECT
						SUM(evt.vlevento)
					FROM wdp.r00002 AS evt
					WHERE evt.idhistorico = h00002.idhistorico
					AND evt.idfuncionario = f00002.idfuncionario
					AND evt.idevento IN('1/326', '001000001I')
				) AS grosspay
			FROM wdp.r00002
			JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			WHERE r00002.dtpagamento::DATE >= '2024-01-01'
			AND TO_CHAR(r00002.dtpagamento + '1month', 'yyyy-mm-07')::DATE <= CURRENT_DATE
			AND f00002.dtdemissao IS null
			AND r00002.tpprocesso = 'M'
			GROUP BY f00002.idfuncionario, r00002.dtpagamento, r00002.tpprocesso, h00002.idhistorico

			UNION

			SELECT
				f00003.idfuncionario,
				f00003.cdchamada,
				f00003.matricula_esocial,
				r00003.tpprocesso,
				r00003.dtpagamento,
				h00003.dshistorico,
				3 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00003 AS evt
					WHERE evt.idhistorico = h00003.idhistorico
					AND evt.idfuncionario = f00003.idfuncionario
					AND evt.sttipoevento = 'FG'
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00003 AS evt
					WHERE evt.idhistorico = h00003.idhistorico
					AND evt.idfuncionario = f00003.idfuncionario
					AND evt.sttipoevento = 'I'
				) AS baseinss,
				(
					SELECT
						SUM(evt.vlevento)
					FROM wdp.r00003 AS evt
					WHERE evt.idhistorico = h00003.idhistorico
					AND evt.idfuncionario = f00003.idfuncionario
					AND evt.idevento IN('1/326', '001000001I')
				) AS grosspay
			FROM wdp.r00003
			JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			JOIN wdp.f00003
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
	 * Busca contra cheque de primeira decimo
	 **/
	private function buscaPrimeiraDecimo(): array
	{
    	$query = "
			SELECT
				f00001.idfuncionario,
				f00001.cdchamada,
				f00001.matricula_esocial,
				r00001.tpprocesso,
				r00001.dtpagamento,
				h00001.dshistorico,
				1 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00001 AS evt
					WHERE evt.idhistorico = h00001.idhistorico
					AND evt.idfuncionario = f00001.idfuncionario
					AND evt.idevento = '0010000009'
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlevento)
					FROM wdp.r00001 AS evt
					WHERE evt.idhistorico = h00001.idhistorico
					AND evt.idfuncionario = f00001.idfuncionario
					AND evt.idevento = '0010000009'
				) AS monthfgts,
				(
					SELECT
						SUM(evt.vlbaseeventobruto)
					FROM wdp.r00001 AS evt
					WHERE evt.idhistorico = h00001.idhistorico
					AND evt.idfuncionario = f00001.idfuncionario
					AND evt.idevento = '0010000009'
				) AS grosspay
			FROM wdp.r00001
			JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND r00001.dtpagamento::DATE <= CURRENT_DATE
			AND f00001.dtdemissao IS null
			AND r00001.tpprocesso = '1'
			GROUP BY f00001.idfuncionario, r00001.dtpagamento, r00001.tpprocesso, h00001.idhistorico

			UNION ALL 

			SELECT
				f00002.idfuncionario,
				f00002.cdchamada,
				f00002.matricula_esocial,
				r00002.tpprocesso,
				r00002.dtpagamento,
				h00002.dshistorico,
				1 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00002 AS evt
					WHERE evt.idhistorico = h00002.idhistorico
					AND evt.idfuncionario = f00002.idfuncionario
					AND evt.idevento = '0010000009'
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlevento)
					FROM wdp.r00002 AS evt
					WHERE evt.idhistorico = h00002.idhistorico
					AND evt.idfuncionario = f00002.idfuncionario
					AND evt.idevento = '0010000009'
				) AS monthfgts,
				(
					SELECT
						SUM(evt.vlbaseeventobruto)
					FROM wdp.r00002 AS evt
					WHERE evt.idhistorico = h00002.idhistorico
					AND evt.idfuncionario = f00002.idfuncionario
					AND evt.idevento = '0010000009'
				) AS grosspay
			FROM wdp.r00002
			JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			WHERE r00002.dtpagamento::date >= '2024-01-01'
			AND r00002.dtpagamento::DATE <= CURRENT_DATE
			AND f00002.dtdemissao IS null
			AND r00002.tpprocesso = '1'
			GROUP BY f00002.idfuncionario, r00002.dtpagamento, r00002.tpprocesso, h00002.idhistorico

			UNION ALL 

			SELECT
				f00003.idfuncionario,
				f00003.cdchamada,
				f00003.matricula_esocial,
				r00003.tpprocesso,
				r00003.dtpagamento,
				h00003.dshistorico,
				1 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00003 AS evt
					WHERE evt.idhistorico = h00003.idhistorico
					AND evt.idfuncionario = f00003.idfuncionario
					AND evt.idevento = '0010000009'
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlevento)
					FROM wdp.r00003 AS evt
					WHERE evt.idhistorico = h00003.idhistorico
					AND evt.idfuncionario = f00003.idfuncionario
					AND evt.idevento = '0010000009'
				) AS monthfgts,
				(
					SELECT
						SUM(evt.vlbaseeventobruto)
					FROM wdp.r00003 AS evt
					WHERE evt.idhistorico = h00003.idhistorico
					AND evt.idfuncionario = f00003.idfuncionario
					AND evt.idevento = '0010000009'
				) AS grosspay
			FROM wdp.r00003
			JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			JOIN wdp.f00003
				ON(f00003.idfuncionario = r00003.idfuncionario)
			WHERE r00003.dtpagamento::date >= '2024-01-01'
			AND r00003.dtpagamento::DATE <= CURRENT_DATE
			AND f00003.dtdemissao IS null
			AND r00003.tpprocesso = '1'
			GROUP BY f00003.idfuncionario, r00003.dtpagamento, r00003.tpprocesso, h00003.idhistorico
		";

		return DBPG::initialize()->select($query);
	}

    /**
     * Busca contra cheque de ferias
     **/
    private function buscaFerias(): array
    {
    	$query = "
			SELECT
				f00001.idfuncionario,
				f00001.cdchamada,
				f00001.matricula_esocial,
				r00001.tpprocesso,
				r00001.dtpagamento,
				h00001.dshistorico,
				1 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00001 AS evt
					WHERE evt.idfuncionario = f00001.idfuncionario
					AND evt.sttipoevento IN('FG', 'FF')
					AND h00001.dtreffinal::DATE
							BETWEEN SYMMETRIC evt.dtinicial::DATE
							AND evt.dtfinal::DATE
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00001 AS evt
					WHERE evt.idfuncionario = f00001.idfuncionario
					AND evt.sttipoevento = 'I'
					AND h00001.dtreffinal::DATE
							BETWEEN SYMMETRIC evt.dtinicial::DATE
							AND evt.dtfinal::DATE
				) AS baseinss,
				wdp.f00001.vlsalariobase AS grosspay
			FROM wdp.r00001
			JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND r00001.dtpagamento::DATE <= CURRENT_DATE
			AND f00001.dtdemissao IS null
			AND r00001.tpprocesso = 'F'
			GROUP BY f00001.idfuncionario, r00001.dtpagamento, r00001.tpprocesso, h00001.idhistorico

			UNION

			SELECT
				f00002.idfuncionario,
				f00002.cdchamada,
				f00002.matricula_esocial,
				r00002.tpprocesso,
				r00002.dtpagamento,
				h00002.dshistorico,
				2 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00002 AS evt
					WHERE evt.idfuncionario = f00002.idfuncionario
					AND evt.sttipoevento IN('FG', 'FF')
					AND h00002.dtreffinal::DATE
							BETWEEN SYMMETRIC evt.dtinicial::DATE
							AND evt.dtfinal::DATE
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00002 AS evt
					WHERE evt.idfuncionario = f00002.idfuncionario
					AND evt.sttipoevento = 'I'
					AND h00002.dtreffinal::DATE
							BETWEEN SYMMETRIC evt.dtinicial::DATE
							AND evt.dtfinal::DATE
				) AS baseinss,
				wdp.f00002.vlsalariobase AS grosspay
			FROM wdp.r00002
			JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			WHERE r00002.dtpagamento::DATE >= '2024-01-01'
			AND r00002.dtpagamento::DATE <= CURRENT_DATE
			AND f00002.dtdemissao IS null
			AND r00002.tpprocesso = 'F'
			GROUP BY f00002.idfuncionario, r00002.dtpagamento, r00002.tpprocesso, h00002.idhistorico

			UNION

			SELECT
				f00003.idfuncionario,
				f00003.cdchamada,
				f00003.matricula_esocial,
				r00003.tpprocesso,
				r00003.dtpagamento,
				h00003.dshistorico,
				3 AS numemp,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00003 AS evt
					WHERE evt.idfuncionario = f00003.idfuncionario
					AND evt.sttipoevento IN('FG', 'FF')
					AND h00003.dtreffinal::DATE
							BETWEEN SYMMETRIC evt.dtinicial::DATE
							AND evt.dtfinal::DATE
				) AS basefgts,
				(
					SELECT
						SUM(evt.vlbaseevento)
					FROM wdp.r00003 AS evt
					WHERE evt.idfuncionario = f00003.idfuncionario
					AND evt.sttipoevento = 'I'
					AND h00003.dtreffinal::DATE
							BETWEEN SYMMETRIC evt.dtinicial::DATE
							AND evt.dtfinal::DATE
				) AS baseinss,
				wdp.f00003.vlsalariobase AS grosspay
			FROM wdp.r00003
			JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			JOIN wdp.f00003
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