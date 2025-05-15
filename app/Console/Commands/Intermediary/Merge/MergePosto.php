<?php

namespace App\Console\Commands\Intermediary\Merge;

use App\Models\Workplace;
use App\Shared\Commands\Command;
use Illuminate\Support\Facades\DB;

class MergePosto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MergePosto';

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
        $postos = Workplace::isPendentToCreate()
                        ->get();

        foreach ($postos as $posto) {
            $found = DB::table('NEXTI_RET_POSTO')
                            ->select('ID')
                            ->where('EXTERNALID', $posto->IDEXTERNO)
                            ->first();
            if (!$found) {
                continue;
            }

            $posto->update([
                'ID' => $found->ID,
                'TIPO' => 0,
                'SITUACAO' => 1,
                'OBSERVACAO' => date('d/m/Y H:i:s') . ' - Registro Já Processado na Nexti (MergePosto)'
            ]);
        }
    }
}