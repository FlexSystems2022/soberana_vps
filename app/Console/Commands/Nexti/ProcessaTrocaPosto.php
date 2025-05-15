<?php

namespace App\Console\Commands\Nexti;

use Illuminate\Support\Collection;
use App\Shared\Commands\CommandNexti;
use App\Models\Workplace\WorkplaceTransfer;
use App\Action\WorkplaceTransfer\CreateWorkplaceTransfer;
use App\Action\WorkplaceTransfer\DeleteWorkplaceTransfer;

class ProcessaTrocaPosto extends CommandNexti
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
    protected $signature = 'ProcessaTrocaPosto';

    /**
     * @param \App\Models\Workplace\WorkplaceTransfer $troca
     * @return void
     **/
    protected function criaRegistro(WorkplaceTransfer $troca): void
    {
        $this->warn("Criando Troca de Posto - [{$troca->IDEXTERNO} - {$troca->INIATU}]");

        $action = new CreateWorkplaceTransfer();

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
     * Busca Troca de Posto no banco
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return WorkplaceTransfer::isPendentToCreate()
                        ->get();
    }

    /**
     * @param \App\Models\Workplace\WorkplaceTransfer $troca
     * @return void
     **/
    protected function deletaRegistro(WorkplaceTransfer $troca): void
    {
        $this->info("Deletando Troca de Posto - [{$troca->IDEXTERNO} - {$troca->INIATU}]");

        $action = new DeleteWorkplaceTransfer();

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
     * Busca Troca de Posto no banco
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return WorkplaceTransfer::isPendentToDelete()
                    ->get();
    }
}