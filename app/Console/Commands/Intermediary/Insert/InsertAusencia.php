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
    protected $signature = 'InsertAusencia';

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

        $sub = DBPG::initialize()->query()->from(function($query) use ($tabelas) {
            foreach ($tabelas as $index => $tabela) {
                $aliasA = 'a' . substr($tabela, 1);

                $builder = DBPG::initialize()->query()->from("wdp.{$aliasA}")
                    ->select([
                        "wdp.{$tabela}.matricula_esocial",
                        "wdp.{$aliasA}.idafastamento",
                        "wdp.{$aliasA}.idespecial",
                        "wdp.{$aliasA}.dtinicial",
                        "wdp.{$aliasA}.dtfinal",
                        "wdp.{$aliasA}.dsmotivo",
                        "wdp.{$aliasA}.cid"
                    ])
                    ->selectRaw("'{$index}' as numemp")
                    ->join("wdp.{$tabela}", "wdp.{$tabela}.idfuncionario", '=', "wdp.{$aliasA}.idfuncionario")
                    ->whereNotNull("wdp.{$tabela}.dtdemissao");

                $query = $index === 0 ? $builder : $query->unionAll($builder);
            }

            return $query;
        }, 'ausencias');

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
            ->fromSub($sub, 'ausencias')
            ->join('wdp.especial', 'wdp.especial.idespecial', '=', 'ausencias.idespecial')
            ->get();
    }

    protected function afterExecute(): void
    {
        $this->createAbsences();
        $this->updateAbsences();
        $this->deleteAbsences();
    }

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
