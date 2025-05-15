<?php

namespace App\Console\Commands\Intermediary\Merge;

use App\Shared\Commands\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Query\JoinClause;
use App\Models\Workplace\WorkplaceTransfer;

class MergeTrocaPosto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MergeTrocaPosto';

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
        $trocas = WorkplaceTransfer::isPendentToCreate()
                        ->get();

        foreach ($trocas as $troca) {
            $found = DB::table('NEXTI_RET_TROCA_POSTO')
                            ->select('id')
                            ->where('PERSONEXTERNALID', $troca->people->IDEXTERNO)
                            ->whereDate('TRANSFERDATETIME', $troca->INIATU?->format('Y-m-d'))
                            ->where('WORKPLACEEXTERNALID', $troca->workplace->IDEXTERNO ?? '')
                            ->first();

            if (!$found) {
                continue;
            }

            $troca->update([
                'ID' => $found->id,
                'TIPO' => 0,
                'SITUACAO' => 1,
                'OBSERVACAO' => date('d/m/Y H:i:s') . ' - Registro Já Processado na Nexti (MergeTrocaPosto)'
            ]);

            DB::table('NEXTI_RET_TROCA_POSTO')
                ->where('id', $found->id)
                ->update([
                    'TIPO' => 0,
                    'SITUACAO' => 1,
                    'OBSERVACAO' => date('d/m/Y H:i:s'). ' - Registro Já Processado na Nexti (MergeTrocaPosto)'
                ]);
        }
    }
}