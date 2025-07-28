<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Absence\AbsenceAux;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use App\Shared\Commands\CommandIntermediary;
use Illuminate\Support\Facades\DB;

class InsertAusencia extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'InsertAusencia';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
    	try {
			AbsenceAux::truncate();

    		return parent::handle();
    	} catch(\Throwable $e) {
    		$this->error($e->getMessage());

    		return static::FAILURE;
    	}
    }

    /**
     * Dispatch Item
     **/
    protected function dispachItem(object $ausencia): string
    {
		$campos = [
			'IDAFASTAMENTO' => $ausencia->idafastamento,
			'NUMEMP' => intval($ausencia->numemp),
			'TIPCOL' => 1,
			'NUMCAD' => $ausencia->matricula_esocial,
			'ABSENCESITUATIONEXTERNALID' => $ausencia->idexternosituacao,
			'FINISHDATETIME' => $ausencia->dtfinal,
			'STARTDATETIME' => $ausencia->dtinicial,
			'IDEXTERNO' => $ausencia->idafastamento . '-' . $ausencia->numemp,
			'CIDCODE' => $ausencia->cid,
			'CIDDESCRICAO' => null,
			'CIDID' => null,
			'DOUTOR_CRM' => null,
			'DOUTOR_NOME' => null,
			'DOUTOR_ID' => null,
			'OBSAFA' => $ausencia->dsmotivo,
		];

		$found = AbsenceAux::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if($found) {
			return "Ausência {$campos['IDEXTERNO']} ignorada!";
		}

		AbsenceAux::create($campos);

		return "Ausência {$campos['IDEXTERNO']} Criado!";
    }

    /**
     * Busca ausencias no banco do cliente
     **/
  protected function buscaRegistros(): Collection
	{
		$tabelas = [
			'a00001' => 'f00001',
			'a00002' => 'f00002',
			'a00003' => 'f00003',
			'a00004' => 'f00004',
			'a00005' => 'f00005',
			'a00006' => 'f00006',
			'a00007' => 'f00007',
			'a00008' => 'f00008',
			'a00009' => 'f00009',
			'a0014'  => 'f0014',
			'a0017'  => 'f0017',
			'a0020'  => 'f0020',
			'a1000'  => 'f1000',
			'a1001'  => 'f1001',
			'a1002'  => 'f1002',
			'a1003'  => 'f1003',
			'a1004'  => 'f1004',
			'a1005'  => 'f1005',
			'a1006'  => 'f1006',
			'a1007'  => 'f1007',
			'a1008'  => 'f1008',
			'a1009'  => 'f1009',
			'a1010'  => 'f1010',
			'a1011'  => 'f1011',
			'a2021'  => 'f2021',
			'a2022'  => 'f2022',
			'a2023'  => 'f2023',
			'a2024'  => 'f2024',
			'a2025'  => 'f2025',
			'a2026'  => 'f2026',
			'a2027'  => 'f2027',
			'a2028'  => 'f2028',
			'a2029'  => 'f2029',
			'a2030'  => 'f2030',
			'a2031'  => 'f2031',
			'a2032'  => 'f2032',
			'a2033'  => 'f2033',
			'a2034'  => 'f2034',
			'a2035'  => 'f2035',
			'a2036'  => 'f2036',
			'a2037'  => 'f2037',
			'a2038'  => 'f2038',
			'a2039'  => 'f2039',
			'a2040'  => 'f2040',
			'a2041'  => 'f2041',
			'a2042'  => 'f2042',
			'a2043'  => 'f2043',
			'a2044'  => 'f2044',
			'a2045'  => 'f2045',
			'a2046'  => 'f2046',
			'a2047'  => 'f2047',
		];

		$unionSqls = [];

		foreach ($tabelas as $aTable => $fTable) {
			$sql = "
				SELECT 
					f.matricula_esocial,
					a.idafastamento,
					a.idespecial,
					a.dtinicial,
					a.dtfinal,
					a.dsmotivo,
					a.cid,
					'{$aTable}' as origem,
					'{$fTable}' as origem_func,
					'{$aTable}' as numemp
				FROM wdp.\"$aTable\" a
				INNER JOIN wdp.\"$fTable\" f ON f.idfuncionario = a.idfuncionario
				WHERE f.dtdemissao IS NULL
			";
			$unionSqls[] = $sql;
		}

		$finalSql = implode(" UNION ALL ", $unionSqls);

		// Agora aplicamos a junção com a tabela especial
		$finalSql = "
			SELECT 
				ausencias.numemp,
				ausencias.matricula_esocial,
				ausencias.idafastamento,
				ausencias.dtinicial,
				ausencias.dtfinal,
				ausencias.dsmotivo,
				ausencias.cid,
				esp.cdchamada AS idexternosituacao
			FROM (
				$finalSql
			) ausencias
			INNER JOIN wdp.especial esp ON esp.idespecial = ausencias.idespecial
		";

		return DB::connection('pgsql')->select($finalSql);
	}



    /**
     * After execute
     */
    protected function afterExecute(): void
    {
    	$this->createAbsences();
    	$this->updateAbsences();
    	$this->deleteAbsences();
    }

    /**
     * Create Absences
     */
    private function createAbsences(): void
    {
    	$query = "
    		INSERT INTO nexti_ausencias_alterdata (IDAFASTAMENTO, NUMEMP, TIPCOL, NUMCAD, ABSENCESITUATIONEXTERNALID, FINISHDATETIME, STARTDATETIME, TIPO, SITUACAO, ID, IDEXTERNO, CIDCODE, CIDDESCRICAO, CIDID, DOUTOR_CRM, DOUTOR_NOME, DOUTOR_ID, OBSAFA)
			SELECT 
			   nexti_ausencias_aux.IDAFASTAMENTO,
			   nexti_ausencias_aux.NUMEMP,
			   nexti_ausencias_aux.TIPCOL,
			   nexti_ausencias_aux.NUMCAD,
			   nexti_ausencias_aux.ABSENCESITUATIONEXTERNALID,
			   nexti_ausencias_aux.FINISHDATETIME,
			   nexti_ausencias_aux.STARTDATETIME,
			   0 AS TIPO,
			   0 AS SITUACAO,
			   0 AS ID,
			   nexti_ausencias_aux.IDEXTERNO,
			   nexti_ausencias_aux.CIDCODE,
			   nexti_ausencias_aux.CIDDESCRICAO,
			   nexti_ausencias_aux.CIDID,
			   nexti_ausencias_aux.DOUTOR_CRM,
			   nexti_ausencias_aux.DOUTOR_NOME,
			   nexti_ausencias_aux.DOUTOR_ID,
			   nexti_ausencias_aux.OBSAFA
			FROM nexti_ausencias_aux
			WHERE NOT EXISTS(
			    SELECT
				 	1
				FROM nexti_ausencias_alterdata
			    WHERE nexti_ausencias_alterdata.IDEXTERNO = nexti_ausencias_aux.IDEXTERNO
			)
    	";

        DB::statement($query);
    }

    /**
     * Update Absence
     */
    private function updateAbsences(): void
    {
    	$query = "
			UPDATE nexti_ausencias_alterdata
			JOIN nexti_ausencias_aux
				ON(nexti_ausencias_aux.IDEXTERNO = nexti_ausencias_alterdata.IDEXTERNO
					AND (nexti_ausencias_aux.ABSENCESITUATIONEXTERNALID <> nexti_ausencias_alterdata.ABSENCESITUATIONEXTERNALID
						OR nexti_ausencias_aux.FINISHDATETIME <> nexti_ausencias_alterdata.FINISHDATETIME
						OR nexti_ausencias_aux.STARTDATETIME <> nexti_ausencias_alterdata.STARTDATETIME
					)
				)
				SET nexti_ausencias_alterdata.ABSENCESITUATIONEXTERNALID = nexti_ausencias_aux.ABSENCESITUATIONEXTERNALID,
					nexti_ausencias_alterdata.FINISHDATETIME = nexti_ausencias_aux.FINISHDATETIME,
					nexti_ausencias_alterdata.STARTDATETIME = nexti_ausencias_aux.STARTDATETIME,
					nexti_ausencias_alterdata.SITUACAO = 0,
					nexti_ausencias_alterdata.TIPO = 1
			WHERE nexti_ausencias_alterdata.TIPO <> 3
			AND nexti_ausencias_alterdata.SITUACAO = 1
    	";

        DB::statement($query);
    }

    /**
     * Delete Absence
     */
    private function deleteAbsences(): void
    {
    	$query = "
			UPDATE nexti_ausencias_alterdata
			SET nexti_ausencias_alterdata.TIPO = 3,
				nexti_ausencias_alterdata.SITUACAO = 0
			WHERE NOT EXISTS(
				SELECT
					1
				FROM nexti_ausencias_aux
				WHERE nexti_ausencias_aux.IDEXTERNO = nexti_ausencias_alterdata.IDEXTERNO
			)
			AND EXISTS(
				SELECT 
					1
				FROM nexti_colaborador
				WHERE nexti_colaborador.NUMEMP = nexti_ausencias_alterdata.NUMEMP
				AND nexti_colaborador.TIPCOL = nexti_ausencias_alterdata.TIPCOL
				AND nexti_colaborador.NUMCAD = nexti_ausencias_alterdata.NUMCAD
				AND nexti_colaborador.DATADEM IS NULL
			)
			AND nexti_ausencias_alterdata.ABSENCESITUATIONEXTERNALID <> '15671'
			AND nexti_ausencias_alterdata.TIPO <> 3
			AND nexti_ausencias_alterdata.ID IS NOT NULL
			AND nexti_ausencias_alterdata.SITUACAO = 1
    	";

        DB::statement($query);

    	$query = "
			UPDATE nexti_ausencias_alterdata
			SET nexti_ausencias_alterdata.TIPO = 3,
				nexti_ausencias_alterdata.SITUACAO = 1,
				nexti_ausencias_alterdata.OBSERVACAO = 'Exclusão via merge'
			WHERE NOT EXISTS(
				SELECT
					1
				FROM nexti_ausencias_aux
				WHERE nexti_ausencias_aux.IDEXTERNO = nexti_ausencias_alterdata.IDEXTERNO
			)
			AND EXISTS(
				SELECT 
					1
				FROM nexti_colaborador
				WHERE nexti_colaborador.NUMEMP = nexti_ausencias_alterdata.NUMEMP
				AND nexti_colaborador.TIPCOL = nexti_ausencias_alterdata.TIPCOL
				AND nexti_colaborador.NUMCAD = nexti_ausencias_alterdata.NUMCAD
				AND nexti_colaborador.DATADEM IS NULL
			)
			AND nexti_ausencias_alterdata.ABSENCESITUATIONEXTERNALID <> '15671'
			AND nexti_ausencias_alterdata.TIPO = 0
			AND nexti_ausencias_alterdata.SITUACAO = 0
    	";

        DB::statement($query);
    }
}