<?php

namespace App\Console\Commands\Nexti;

use App\Models\Shift;
use App\Action\Shift\CreateShift;
use App\Action\Shift\UpdateShift;
use App\Action\Shift\DeleteShift;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandNexti;

class ProcessaHorario extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaHorario';

    /**
     * @param \App\Models\Shift $horario
     * @return void
     **/
    protected function criaRegistro(Shift $horario): void
    {
        $this->info("Criando Horario {$horario->DESHOR}");

        $action = new CreateShift();

        $response = $action->create($this->client(), $horario);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($horario->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $horario->update($update);
            return;
        }

        $horario->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Horarios no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Shift>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Shift::isPendentToCreate()
                    ->get();
    }

    /**
     * @param \App\Models\Shift $horario
     * @return void
     **/
    protected function atualizaRegistro(Shift $horario): void
    {
        $this->info("Atualizando Horario {$horario->DESHOR}");

        $action = new UpdateShift();

        $response = $action->update($this->client(), $horario);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($horario->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $horario->update($update);
            return;
        }

        $horario->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Horarios no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Shift>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Shift::isPendentToUpdate()
                    ->get();
    }

    /**
     * @param \App\Models\Shift $horario
     * @return void
     **/
    protected function deletaRegistro(Shift $horario): void
    {
        $this->info("Excluindo Horario {$horario->DESHOR}");

        $action = new DeleteShift();

        $response = $action->delete($this->client(), $horario);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($horario->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $horario->update($update);
            return;
        }

        $horario->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Horario no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Shift>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return Shift::isPendentToDelete()
                    ->get();
    }
}