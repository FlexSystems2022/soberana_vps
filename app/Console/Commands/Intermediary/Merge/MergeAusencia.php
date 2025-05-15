<?php

namespace App\Console\Commands\Intermediary\Merge;

use App\Models\Absence;
use App\Shared\Helper;
use App\Shared\Commands\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class MergeAusencia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MergeAusencia';

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
        $ausencias = Absence::isPendentToCreate()
                        ->get();
		
        foreach ($ausencias as $ausencia) {
            $found = DB::table('NEXTI_RET_AUSENCIAS')
                            ->select('ID')
                            ->where('PERSONEXTERNALID', $ausencia->people->IDEXTERNO)
                            ->when($ausencia->FINISHDATETIME,
                                fn(Builder $where) => $where->whereDate('FINISHDATETIME', 
                                                            $ausencia->FINISHDATETIME->format('Y-m-d')
                                                        ),
                                fn(Builder $where) => $where->where('FINISHDATETIME', null)
                            )
                            ->whereDate('STARTDATETIME', 
                                $ausencia->STARTDATETIME?->format('Y-m-d')
                            )
                            ->where('ABSENCESITUATIONEXTERNALID', $ausencia->ABSENCESITUATIONEXTERNALID)
                            ->where('TIPO', 0)
                            ->first();
            if (!$found) {
                continue;
            }

            $ausencia->update([
                'ID' => $found->ID,
                'SITUACAO' => 1,
                'OBSERVACAO' => date('d/m/Y H:i:s') . ' - Registro Já Processado na Nexti (recuperaId)'
            ]);

            DB::table('NEXTI_RET_AUSENCIAS')
                ->where('ID', $found->ID)
                ->update([
                    'SITUACAO' => 1,
                    'OBSERVACAO' => date('d/m/Y H:i:s'). ' - Registro Já Processado na Nexti (recuperaId)',
                ]);
        }
    }
}