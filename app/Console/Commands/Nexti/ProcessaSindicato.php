<?php

namespace App\Console\Commands\Nexti;

use App\Models\Union;
use App\Action\Union\CreateUnion;
use App\Action\Union\UpdateUnion;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandNexti;

class ProcessaSindicato extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaSindicato';

    /**
     * @param \App\Models\Union $sindicato
     * @return mixed
     **/
    protected function criaRegistro(Union $sindicato): void
    {
        $this->info("Criando Sindicato {$sindicato->NOMSIN}");

        $action = new CreateUnion();

        $response = $action->create($this->client(), $sindicato);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($sindicato->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $sindicato->update($update);
            return;
        }

        $sindicato->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Sindicatos no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Union>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Union::isPendentToCreate()
                    ->get();
    }

    /**
     * @param object $sindicato
     * @return mixed
     **/
    protected function atualizaRegistro(object $sindicato): void
    {
        $this->info("Atualizando Sindicato {$sindicato->NOMSIN}");

        $action = new UpdateUnion();

        $response = $action->update($this->client(), $sindicato);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($sindicato->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $sindicato->update($update);
            return;
        }

        $sindicato->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Sindicatos no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Union>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Union::isPendentToUpdate()
                    ->get();
    }
}