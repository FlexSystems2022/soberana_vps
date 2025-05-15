<?php

namespace App\Console\Commands\Intermediary\Merge;

use App\Shared\Commands\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Query\JoinClause;
use App\Models\Schedule\ScheduleTransfer;

class MergeTrocaEscala extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MergeTrocaEscala';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Concilia Registros Entre Envio e Retorno x Nexti.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->recuperaId();
    }

    /**
     * Recupera ID By Merge
     *
     * @return void
     **/
    public function recuperaId(): void
    {
        $trocas = ScheduleTransfer::isPendentToCreate()
                        ->get();
		
        foreach ($trocas as $troca) {
            if(!$troca->people) continue;

            $found = DB::table('NEXTI_RET_TROCA_ESCALA')
                            ->select('id')
                            ->where('PERSONEXTERNALID', $troca->people->IDEXTERNO)
                            ->where('SCHEDULEID', $troca->ESCALA)
                            ->whereDate('TRANSFERDATETIME', $troca->DATALT?->format('Y-m-d'))
                            ->where('ROTATIONCODE', $troca->TURMA)
                            ->where('TIPO', 0)
                            ->first();

            if (!$found) {
                continue;
            }

            $troca->update([
                'ID' => $found->id,
                'TIPO' => 0,
                'SITUACAO' => 1,
                'OBSERVACAO' => date('d/m/Y H:i:s') . ' - Registro Já Processado na Nexti (MergeTrocaEscala)'
            ]);

            DB::table('NEXTI_RET_TROCA_ESCALA')
                ->where('id', $found->id)
                ->update([
                    'TIPO' => 0,
                    'SITUACAO' => 1,
                    'OBSERVACAO' => date('d/m/Y H:i:s'). ' - Registro Já Processado na Nexti (MergeTrocaEscala)',
                ]);
        }
    }
}