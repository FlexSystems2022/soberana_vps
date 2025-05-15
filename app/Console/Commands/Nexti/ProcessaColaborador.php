<?php

namespace App\Console\Commands\Nexti;

use App\Models\People;
use Illuminate\Support\Collection;
use App\Action\People\CreatePeople;
use App\Action\People\UpdatePeople;
use App\Action\People\DeletePeople;
use App\Shared\Commands\CommandNexti;

class ProcessaColaborador extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaColaborador';

    /**
     * @param \App\Models\People $colaborador
     * @return void
     **/
    protected function criaRegistro(People $colaborador): void
    {
        $this->info("Criando Colaborador {$colaborador->NOMFUN}");

        $action = new CreatePeople();

        $response = $action->create($this->client(), $colaborador);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($colaborador->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $colaborador->update($update);
            return;
        }

        $colaborador->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Colaboradores no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\People>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return People::isPendentToCreate()
                    ->get();
    }

    /**
     * @param \App\Models\People $colaborador
     * @return void
     **/
    protected function atualizaRegistro(People $colaborador): void
    {
        $this->info("Atualizando Colaborador {$colaborador->NOMFUN}");

        $action = new UpdatePeople();

        $response = $action->update($this->client(), $colaborador);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($colaborador->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $colaborador->update($update);
            return;
        }

        $colaborador->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Colaborador no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\People>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return People::isPendentToUpdate()
                    ->get();
    }

    /**
     * @param \App\Models\People $colaborador
     * @return void
     **/
    protected function deletaRegistro(People $colaborador): void
    {
        $this->info("Excluindo Colaborador {$colaborador->NOMFUN}");

        $action = new DeletePeople();

        $response = $action->delete($this->client(), $colaborador);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($colaborador->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $colaborador->update($update);
            return;
        }

        $colaborador->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Colaboradores no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\People>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return People::isPendentToDelete()
                    ->get();
    }
}