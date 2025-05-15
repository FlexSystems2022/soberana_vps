<?php

namespace App\Console\Commands\Nexti;

use Illuminate\Support\Collection;
use App\Shared\Commands\CommandNexti;
use App\Models\Schedule\ScheduleTransfer;
use App\Action\ScheduleTransfer\CreateScheduleTransfer;
use App\Action\ScheduleTransfer\DeleteScheduleTransfer;

class ProcessaTrocaEscala extends CommandNexti
{
    /**
     * Request timeinterval in seconds
     * 
     * @var float
     */
    protected float $timeInterval = 2;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaTrocaEscala';

    /**
     * @param \App\Models\Schedule\ScheduleTransfer $troca
     * @return void
     **/
    protected function criaRegistro(ScheduleTransfer $troca): void
    {
        $this->warn("Criando Troca Escala - [{$troca->IDEXTERNO} - {$troca->people->IDEXTERNO}]");

        $action = new CreateScheduleTransfer();

        $response = $action->create($this->client(), $troca);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($troca->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $troca->update($update);

            return;
        }

        $troca->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Troca Escalas no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Schedule\ScheduleTransfer>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return ScheduleTransfer::isPendentToCreate()
                    ->orderBy('DATALT')
                    ->get();
    }

    /**
     * @param \App\Models\Schedule\ScheduleTransfer $troca
     * @return void
     **/
    protected function deletaRegistro(ScheduleTransfer $troca): void
    {
        $this->info("Deletando Troca Escala - [{$troca->IDEXTERNO}]");

        $action = new DeleteScheduleTransfer();

        $response = $action->delete($this->client(), $troca);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($troca->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $troca->update($update);

            return;
        }

        $troca->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Troca Escalas no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Schedule\ScheduleTransfer>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return ScheduleTransfer::isPendentToDelete()
                    ->orderBy('DATALT')
                    ->get();
    }
}