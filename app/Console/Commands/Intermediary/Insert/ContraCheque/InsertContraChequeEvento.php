<?php

namespace App\Console\Commands\Intermediary\Insert\ContraCheque;

use App\Shared\DBPG;
use App\Shared\Helper;
use App\Models\Paycheck\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandIntermediary;
use App\Console\Commands\Intermediary\Shared\IdExternoContraCheque;

class InsertContraChequeEvento extends CommandIntermediary
{
	use IdExternoContraCheque;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'InsertContraChequeEvento';

    /**
     * Dispatch Item
     **/
    protected function dispachItem(object $evento): string
    {
    	$evento->dtpagamento = $evento->dtpagamento ? Carbon::parse($evento->dtpagamento) : null;

		$campos = [
			'ID' => $evento->idresumo,
			'COST' => $evento->vlevento ?? 0,
			'DESCRIPTION' => trim($evento->nmevento),
			'REFERENCE' => null,
			'PAYCHECKRECORDTYPEID' => $evento->sttipo === 'D' ? 2 : 1,
			'ID_CONTRA_CHEQUE' => $this->generateIdExterno($evento),
			'LOTE' => $this->getKeyCommand()
		];

		if($evento->idevento == '1/326') {
			$campos['REFERENCE'] = Helper::minutesToHours($evento->nrmintrabalhados);
		} else if($evento->sttipoevento == 'HE') {
			$campos['REFERENCE'] = Helper::minutesToHours($evento->qtevento);
		}

		$found = Event::where('ID', $campos['ID'])->first();
		if(!$found) {
			Event::create($campos);

			return "Evento Contra Cheque {$campos['ID']} criado!";
		}

		$found->update($campos);

		return "Evento Contra Cheque {$campos['ID']} atualizado!";
    }

    /**
     * Busca eventos no banco do cliente
     **/
    protected function buscaRegistros(): Collection
    {
		return collect()
				->merge($this->buscaMensal())
				->merge($this->buscaPrimeiraDecimo())
				->merge($this->buscaFerias());
	}

	/**
	 * Busca eventos mensal
	 **/
	private function buscaMensal(): array
	{
    	$query = "
			SELECT
				r00001.idresumo,
				h00001.dshistorico,
				r00001.dtpagamento,
				r00001.tpprocesso,
				f00001.cdchamada,
				r00001.vlbaseevento AS valor,
				r00001.vlevento,
				r00001.nrmintrabalhados,
				r00001.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				1 AS numemp
			FROM wdp.r00001
			JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00001.idevento)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND TO_CHAR(r00001.dtpagamento + '1month', 'yyyy-mm-07')::DATE <= CURRENT_DATE
			AND r00001.sttipoevento NOT IN('CS', 'FG')
			AND NOT evento.stpgto = 'N'
			AND f00001.dtdemissao IS null
			AND r00001.tpprocesso = 'M'

			UNION ALL

			SELECT
				r00002.idresumo,
				h00002.dshistorico,
				r00002.dtpagamento,
				r00002.tpprocesso,
				f00002.cdchamada,
				r00002.vlbaseevento AS valor,
				r00002.vlevento,
				r00002.nrmintrabalhados,
				r00002.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				2 AS numemp
			FROM wdp.r00002
			JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00002.idevento)
			WHERE r00002.dtpagamento::DATE >= '2024-01-01'
			AND TO_CHAR(r00002.dtpagamento + '1month', 'yyyy-mm-07')::DATE <= CURRENT_DATE
			AND r00002.sttipoevento NOT IN('CS', 'FG')
			AND NOT evento.stpgto = 'N'
			AND f00002.dtdemissao IS null
			AND r00002.tpprocesso = 'M'

			UNION ALL

			SELECT
				r00003.idresumo,
				h00003.dshistorico,
				r00003.dtpagamento,
				r00003.tpprocesso,
				f00003.cdchamada,
				r00003.vlbaseevento AS valor,
				r00003.vlevento,
				r00003.nrmintrabalhados,
				r00003.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				3 AS numemp
			FROM wdp.r00003
			JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			JOIN wdp.f00003
				ON(f00003.idfuncionario = r00003.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00003.idevento)
			WHERE r00003.dtpagamento::DATE >= '2024-01-01'
			AND TO_CHAR(r00003.dtpagamento + '1month', 'yyyy-mm-07')::DATE <= CURRENT_DATE
			AND r00003.sttipoevento NOT IN('CS', 'FG')
			AND NOT evento.stpgto = 'N'
			AND f00003.dtdemissao IS null
			AND r00003.tpprocesso = 'M'
		";

		return DBPG::initialize()->select($query);
	}

	/**
	 * Busca eventos primeira decimo
	 **/
	private function buscaPrimeiraDecimo(): array
	{
    	$query = "
			SELECT
				r00001.idresumo,
				h00001.dshistorico,
				r00001.dtpagamento,
				r00001.tpprocesso,
				f00001.cdchamada,
				r00001.vlbaseevento AS valor,
				r00001.vlevento,
				r00001.nrmintrabalhados,
				r00001.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				1 AS numemp
			FROM wdp.r00001
			JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00001.idevento)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND r00001.dtpagamento::DATE <= CURRENT_DATE
			AND r00001.sttipoevento NOT IN('CS', 'FG')
			AND NOT evento.stpgto = 'N'
			AND f00001.dtdemissao IS null
			AND r00001.tpprocesso = '1'

			UNION ALL

			SELECT
				r00002.idresumo,
				h00002.dshistorico,
				r00002.dtpagamento,
				r00002.tpprocesso,
				f00002.cdchamada,
				r00002.vlbaseevento AS valor,
				r00002.vlevento,
				r00002.nrmintrabalhados,
				r00002.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				1 AS numemp
			FROM wdp.r00002
			JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00002.idevento)
			WHERE r00002.dtpagamento::DATE >= '2024-01-01'
			AND r00002.dtpagamento::DATE <= CURRENT_DATE
			AND r00002.sttipoevento NOT IN('CS', 'FG')
			AND NOT evento.stpgto = 'N'
			AND f00002.dtdemissao IS null
			AND r00002.tpprocesso = '1'

			UNION ALL

			SELECT
				r00003.idresumo,
				h00003.dshistorico,
				r00003.dtpagamento,
				r00003.tpprocesso,
				f00003.cdchamada,
				r00003.vlbaseevento AS valor,
				r00003.vlevento,
				r00003.nrmintrabalhados,
				r00003.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				1 AS numemp
			FROM wdp.r00003
			JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			JOIN wdp.f00003
				ON(f00003.idfuncionario = r00003.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00003.idevento)
			WHERE r00003.dtpagamento::DATE >= '2024-01-01'
			AND r00003.dtpagamento::DATE <= CURRENT_DATE
			AND r00003.sttipoevento NOT IN('CS', 'FG')
			AND NOT evento.stpgto = 'N'
			AND f00003.dtdemissao IS null
			AND r00003.tpprocesso = '1'
		";

		return DBPG::initialize()->select($query);
	}

    /**
     * Busca eventos de ferias
     **/
    private function buscaFerias(): array
    {
    	$query = "
			SELECT
				r00001.idresumo,
				h00001.dshistorico,
				ferias.dtpagamento,
				ferias.tpprocesso,
				f00001.cdchamada,
				r00001.vlbaseevento AS valor,
				r00001.vlevento,
				r00001.nrmintrabalhados,
				r00001.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				1 AS numemp
			FROM wdp.r00001
			JOIN wdp.h00001
				ON(h00001.idhistorico = r00001.idhistorico)
			JOIN wdp.f00001
				ON(f00001.idfuncionario = r00001.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00001.idevento)
			JOIN(
				SELECT
					funcionario_ferias.idfuncionario,
					ferias.tpprocesso,
					ferias.dtpagamento,
					historico_ferias.dtreffinal
				FROM wdp.r00001 AS ferias
				JOIN wdp.h00001 AS historico_ferias
					ON(historico_ferias.idhistorico = ferias.idhistorico)
				JOIN wdp.f00001 AS funcionario_ferias
					ON(funcionario_ferias.idfuncionario = ferias.idfuncionario)
				WHERE ferias.tpprocesso = 'F'
				AND ferias.dtpagamento::DATE >= '2024-01-01'
				AND funcionario_ferias.dtdemissao IS NULL
				GROUP BY funcionario_ferias.idfuncionario, ferias.dtpagamento, ferias.tpprocesso, historico_ferias.idhistorico
			) AS ferias
				ON(ferias.idfuncionario = r00001.idfuncionario
					AND ferias.dtreffinal::DATE
						BETWEEN SYMMETRIC r00001.dtinicial::DATE
										AND r00001.dtfinal::DATE
				)
			WHERE r00001.dtpagamento::DATE >= '2024-01-01'
			AND r00001.dtpagamento::DATE <= CURRENT_DATE
			AND r00001.sttipoevento NOT IN('CS', 'R')
			AND NOT evento.stpgto = 'N'
			AND f00001.dtdemissao IS null

			UNION ALL

			SELECT
				r00002.idresumo,
				h00002.dshistorico,
				ferias.dtpagamento,
				ferias.tpprocesso,
				f00002.cdchamada,
				r00002.vlbaseevento AS valor,
				r00002.vlevento,
				r00002.nrmintrabalhados,
				r00002.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				2 AS numemp
			FROM wdp.r00002
			JOIN wdp.h00002
				ON(h00002.idhistorico = r00002.idhistorico)
			JOIN wdp.f00002
				ON(f00002.idfuncionario = r00002.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00002.idevento)
			JOIN(
				SELECT
					funcionario_ferias.idfuncionario,
					ferias.tpprocesso,
					ferias.dtpagamento,
					historico_ferias.dtreffinal
				FROM wdp.r00002 AS ferias
				JOIN wdp.h00002 AS historico_ferias
					ON(historico_ferias.idhistorico = ferias.idhistorico)
				JOIN wdp.f00002 AS funcionario_ferias
					ON(funcionario_ferias.idfuncionario = ferias.idfuncionario)
				WHERE ferias.tpprocesso = 'F'
				AND ferias.dtpagamento::DATE >= '2024-01-01'
				AND funcionario_ferias.dtdemissao IS NULL
				GROUP BY funcionario_ferias.idfuncionario, ferias.dtpagamento, ferias.tpprocesso, historico_ferias.idhistorico
			) AS ferias
				ON(ferias.idfuncionario = r00002.idfuncionario
					AND ferias.dtreffinal::DATE
						BETWEEN SYMMETRIC r00002.dtinicial::DATE
										AND r00002.dtfinal::DATE
				)
			WHERE r00002.dtpagamento::DATE >= '2024-01-01'
			AND r00002.dtpagamento::DATE <= CURRENT_DATE
			AND r00002.sttipoevento NOT IN('CS', 'R')
			AND NOT evento.stpgto = 'N'
			AND f00002.dtdemissao IS null

			UNION ALL

			SELECT
				r00003.idresumo,
				h00003.dshistorico,
				ferias.dtpagamento,
				ferias.tpprocesso,
				f00003.cdchamada,
				r00003.vlbaseevento AS valor,
				r00003.vlevento,
				r00003.nrmintrabalhados,
				r00003.qtevento,
				evento.idevento,
				evento.nmevento,
				evento.sttipoevento,
				evento.sttipo,
				3 AS numemp
			FROM wdp.r00003
			JOIN wdp.h00003
				ON(h00003.idhistorico = r00003.idhistorico)
			JOIN wdp.f00003
				ON(f00003.idfuncionario = r00003.idfuncionario)
			JOIN wdp.evento
				ON(evento.idevento = r00003.idevento)
			JOIN(
				SELECT
					funcionario_ferias.idfuncionario,
					ferias.tpprocesso,
					ferias.dtpagamento,
					historico_ferias.dtreffinal
				FROM wdp.r00003 AS ferias
				JOIN wdp.h00003 AS historico_ferias
					ON(historico_ferias.idhistorico = ferias.idhistorico)
				JOIN wdp.f00003 AS funcionario_ferias
					ON(funcionario_ferias.idfuncionario = ferias.idfuncionario)
				WHERE ferias.tpprocesso = 'F'
				AND ferias.dtpagamento::DATE >= '2024-01-01'
				AND funcionario_ferias.dtdemissao IS NULL
				GROUP BY funcionario_ferias.idfuncionario, ferias.dtpagamento, ferias.tpprocesso, historico_ferias.idhistorico
			) AS ferias
				ON(ferias.idfuncionario = r00003.idfuncionario
					AND ferias.dtreffinal::DATE
						BETWEEN SYMMETRIC r00003.dtinicial::DATE
										AND r00003.dtfinal::DATE
				)
			WHERE r00003.dtpagamento::DATE >= '2024-01-01'
			AND r00003.dtpagamento::DATE <= CURRENT_DATE
			AND r00003.sttipoevento NOT IN('CS', 'R')
			AND NOT evento.stpgto = 'N'
			AND f00003.dtdemissao IS null
		";

		return DBPG::initialize()->select($query);
    }

    /**
     * After execute
     */
    protected function afterExecute(): void
    {
		Event::whereNot('LOTE', $this->getKeyCommand())
				->orWhere('LOTE', null)
				->delete();
    }
}