<?php

namespace App\Console\Commands\Nexti;

use App\Models\Client;
use Illuminate\Support\Collection;
use App\Action\Client\CreateClient;
use App\Action\Client\DeleteClient;
use App\Action\Client\UpdateClient;
use App\Shared\Commands\CommandNexti;

class ProcessaCliente extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaCliente';

    /**
     * @param \App\Models\Client $cliente
     * @return mixed
     **/
    protected function criaRegistro(Client $cliente): void
    {
        $this->info("Criando Cliente {$cliente->NOMOEM}");

        $action = new CreateClient();

        $response = $action->create($this->client(), $cliente);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($cliente->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $cliente->update($update);

            return;
        }

        $cliente->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Clientes no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Client>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Client::isPendentToCreate()
                    ->get();
    }

    /**
     * @param \App\Models\Client $cliente
     * @return mixed
     **/
    protected function atualizaRegistro(Client $cliente): void
    {
        $this->info("Atualizando Cliente {$cliente->NOMOEM}");

        $action = new UpdateClient();

        $response = $action->update($this->client(), $cliente);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($cliente->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $cliente->update($update);

            return;
        }

        $cliente->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Clientes no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Client>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Client::isPendentToUpdate()
                    ->get();
    }

    /**
     * @param \App\Models\Client $cliente
     * @return mixed
     **/
    protected function deletaRegistro(Client $cliente): void
    {
        $this->info("Excluindo Cliente {$cliente->NOMOEM}");

        $action = new DeleteClient();

        $response = $action->delete($this->client(), $cliente);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($cliente->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $cliente->update($update);
            return;
        }

        $cliente->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Clientes no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Client>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return Client::isPendentToDelete()
                    ->get();
    }
}