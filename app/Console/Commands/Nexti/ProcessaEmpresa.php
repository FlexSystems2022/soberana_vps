<?php

namespace App\Console\Commands\Nexti;

use App\Models\Company;
use Illuminate\Support\Collection;
use App\Action\Company\CreateCompany;
use App\Action\Company\DeleteCompany;
use App\Action\Company\UpdateCompany;
use App\Shared\Commands\CommandNexti;

class ProcessaEmpresa extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaEmpresa';

    /**
     * @param \App\Models\Company $empresa
     * @return mixed
     **/
    protected function criaRegistro(Company $empresa): void
    {
        $this->info("Criando Empresa {$empresa->RAZSOC}");

        $action = new CreateCompany();

        $response = $action->create($this->client(), $empresa);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($empresa->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $empresa->update($update);

            return;
        }

        $empresa->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Empresas no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Company>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Company::isPendentToCreate()
                    ->get();
    }

    /**
     * @param \App\Models\Company $empresa
     * @return mixed
     **/
    protected function atualizaRegistro(Company $empresa): void
    {
        $this->info("Atualizando Empresa {$empresa->RAZSOC}");

        $action = new UpdateCompany();

        $response = $action->update($this->client(), $empresa);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($empresa->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $empresa->update($update);

            return;
        }

        $empresa->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Empresas no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Company>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Company::isPendentToUpdate()
                    ->get();
    }

    /**
     * @param \App\Models\Company $empresa
     * @return mixed
     **/
    protected function deletaRegistro(Company $empresa): void
    {
        $this->info("Excluindo Empresa {$empresa->RAZSOC}");

        $action = new DeleteCompany();

        $response = $action->delete($this->client(), $empresa);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($empresa->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $empresa->update($update);
            return;
        }

        $empresa->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Empresa no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Company>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return Company::isPendentToDelete()
                    ->get();
    }
}