<?php

namespace App\Console\Commands\Nexti;

use App\Models\Absence;
use Illuminate\Support\Collection;
use App\Action\Absence\CreateAbsence;
use App\Action\Absence\DeleteAbsence;
use App\Shared\Commands\CommandNexti;

class ProcessaAusencia extends CommandNexti
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
    protected $signature = 'ProcessaAusencia';

    /**
     * Create ausencia
     **/
    protected function criaRegistro(Absence $ausencia): void
    {
        $this->warn("Criando Ausência - [{$ausencia->IDEXTERNO} - {$ausencia->ABSENCESITUATIONEXTERNALID}]");

        $action = new CreateAbsence();

        $response = $action->create($this->client(), $ausencia);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($ausencia->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $ausencia->update($update);

            return;
        }

        $ausencia->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Ausencia no banco
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Absence::isPendentToCreate()
                        ->get();
    }

    /**
     * Update ausencia
     **/
    protected function atualizaRegistro(Absence $ausencia): void
    {
        $this->info("Atualizando Ausência [{$ausencia->IDEXTERNO} - {$ausencia->ABSENCESITUATIONEXTERNALID}]");

        try {
            $this->deletaRegistro($ausencia);
        } catch(\Throwable) {
            $this->error('Erro ao excluir Ausência');
        }

        $this->criaRegistro($ausencia);
    }

    /**
     * Busca Ausencias no banco
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Absence::isPendentToUpdate()
                    ->whereNot('IDEXTERNO', 'ZZZG7E84040C0B1821E04EF2EDB5A139D3-1')
                    ->orderBy('STARTDATETIME')
                    ->get();
    }

    /**
     * Delete Absence
     **/
    protected function deletaRegistro(Absence $ausencia): void
    {
        $this->info("Deletando Ausência - [{$ausencia->IDEXTERNO} - {$ausencia->ABSENCESITUATIONEXTERNALID}]");

        $action = new DeleteAbsence();

        $response = $action->delete($this->client(), $ausencia);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($ausencia->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $ausencia->update($update);
            return;
        }

        $ausencia->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Ausência no banco
     * 
     * @return \Illuminate\Support\Collection
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return Absence::isPendentToDelete()
                    ->orderBy('STARTDATETIME')
                    ->get();
    }
}