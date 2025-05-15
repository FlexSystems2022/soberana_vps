<?php

namespace App\Console\Commands\Nexti;

use App\Models\Absence\Situation;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandNexti;
use App\Action\AbsenceSituation\CreateAbsenceSituation;
use App\Action\AbsenceSituation\UpdateAbsenceSituation;

class ProcessaSituacao extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaSituacao';

    /**
     * @param \App\Models\Absence\Situation $situacao
     * @return void
     **/
    protected function criaRegistro(Situation $situacao): void
    {
        $this->info("Criando Situação {$situacao->DESSIT}");

        $action = new CreateAbsenceSituation();

        $response = $action->create($this->client(), $situacao);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($situacao->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $situacao->update($update);
            return;
        }

        $situacao->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Situacões no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Absence\Situation>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Situation::isPendentToCreate()
                    ->get();
    }

    /**
     * @param \App\Models\Absence\Situation $situacao
     * @return void
     **/
    protected function atualizaRegistro(Situation $situacao): void
    {
        $this->info("Atualizando Situação {$situacao->DESSIT}");

        $action = new UpdateAbsenceSituation();

        $response = $action->update($this->client(), $situacao);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($situacao->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $situacao->update($update);
            return;
        }

        $situacao->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Situacões no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Absence\Situation>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Situation::isPendentToUpdate()
                    ->get();
    }
}