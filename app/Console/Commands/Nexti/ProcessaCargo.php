<?php

namespace App\Console\Commands\Nexti;

use App\Models\Career;
use Illuminate\Support\Collection;
use App\Action\Carrer\CreateCarrer;
use App\Action\Carrer\DeleteCarrer;
use App\Action\Carrer\UpdateCarrer;
use App\Shared\Commands\CommandNexti;

class ProcessaCargo extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaCargo';

    /**
     * @param \App\Models\Career $cargo
     * @return mixed
     **/
    protected function criaRegistro(Career $cargo): void
    {
        $this->info("Criando Cargo {$cargo->TITCAR}");

        $action = new CreateCarrer();

        $response = $action->create($this->client(), $cargo);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($cargo->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $cargo->update($update);

            return;
        }

        $cargo->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - criado na Nexti'
        ]);
    }

    /**
     * Busca Cargos no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Career>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Career::isPendentToCreate()
                    ->get();
    }

    /**
     * @param \App\Models\Career $cargo
     * @return mixed
     **/
    protected function atualizaRegistro(Career $cargo): void
    {
        $this->info("Atualizando Cargo {$cargo->TITCAR}");

        $action = new UpdateCarrer();

        $response = $action->update($this->client(), $cargo);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($cargo->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $cargo->update($update);

            return;
        }

        $cargo->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Cargos no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Career>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Career::isPendentToUpdate()
                    ->get();
    }

    /**
     * @param \App\Models\Career $cargo
     * @return mixed
     **/
    protected function deletaRegistro(Career $cargo): void
    {
        $this->info("Excluindo Cargo {$cargo->TITCAR}");

        $action = new DeleteCarrer();

        $response = $action->delete($this->client(), $cargo);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($cargo->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $cargo->update($update);
            return;
        }

        $cargo->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Cargos no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Career>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return Career::isPendentToDelete()
                    ->get();
    }
}