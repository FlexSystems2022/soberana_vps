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
        return DBPG::initialize()
        	->query()
            ->select([
                'ausencias.numemp',
                'ausencias.matricula_esocial',
                'ausencias.idafastamento',
                'ausencias.dtinicial',
                'ausencias.dtfinal',
                'ausencias.dsmotivo',
                'ausencias.cid',
            ])
            ->selectRaw('wdp.especial.cdchamada AS idexternosituacao')
            ->fromSub(
            	fn(Builder $db) => $db->from('wdp.a00001')
				                    ->select([
				                        'wdp.f00001.matricula_esocial',
				                        'wdp.a00001.idafastamento',
				                        'wdp.a00001.idespecial',
				                        'wdp.a00001.dtinicial',
				                        'wdp.a00001.dtfinal',
				                        'wdp.a00001.dsmotivo',
				                        'wdp.a00001.cid',
				                    ])
				                    ->selectRaw("'1' as numemp")
				                    ->join('wdp.f00001',
				                    	'wdp.f00001.idfuncionario', '=', 'wdp.a00001.idfuncionario'
				                    )
				                    ->where('wdp.f00001.dtdemissao')
				                    ->union(
					                    fn(Builder $union) =>
						                    $union->from('wdp.a00002')
							                    ->select([
							                        'wdp.f00002.matricula_esocial',
							                        'wdp.a00002.idafastamento',
							                        'wdp.a00002.idespecial',
							                        'wdp.a00002.dtinicial',
							                        'wdp.a00002.dtfinal',
							                        'wdp.a00002.dsmotivo',
							                        'wdp.a00002.cid',
							                    ])
							                    ->selectRaw("'2' as numemp")
							                    ->join('wdp.f00002', 'wdp.f00002.idfuncionario', '=', 'wdp.a00002.idfuncionario')
							                    ->where('wdp.f00002.dtdemissao')
					                )
				                    ->union(
					                    fn(Builder $union) =>
						                    $union->from('wdp.a00003')
							                    ->select([
							                        'wdp.f00003.matricula_esocial',
							                        'wdp.a00003.idafastamento',
							                        'wdp.a00003.idespecial',
							                        'wdp.a00003.dtinicial',
							                        'wdp.a00003.dtfinal',
							                        'wdp.a00003.dsmotivo',
							                        'wdp.a00003.cid',
							                    ])
							                    ->selectRaw("'3' as numemp")
							                    ->join('wdp.f00003', 'wdp.f00003.idfuncionario', '=', 'wdp.a00003.idfuncionario')
							                    ->where('wdp.f00003.dtdemissao')
					                ),
				'ausencias'
            )
            ->join('wdp.especial',
            	'wdp.especial.idespecial', '=', 'ausencias.idespecial'
            )
            ->get();
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