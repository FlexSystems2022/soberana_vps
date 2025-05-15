<?php

namespace App\Console\Commands\Nexti;

use App\Models\Paycheck;
use App\Shared\Enums\TypeEnum;
use Illuminate\Support\Collection;
use App\Shared\Enums\SituationEnum;
use App\Shared\Commands\CommandNexti;
use App\Action\Paycheck\CreatePaycheck;
use App\Action\Paycheck\DeletePaycheck;

class ProcessaContraCheque extends CommandNexti
{
    /**
     * @var bool
     */
    protected bool $log = true;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ProcessaContraCheque';

    /**
     * @param \App\Models\Paycheck $contracheque
     * @return void
     **/
    protected function criaRegistro(Paycheck $contracheque): void
    {
    	if($contracheque->SITUACAO == SituationEnum::Error) {
    		try {
		        $this->warn("Excluindo Contra Cheque - [{$contracheque->NUMEMP} - {$contracheque->IDEXTERNO}]");

				$this->deletaRegistro($contracheque);
    		} catch(\Throwable) {
		        $this->error('Erro ao excluir Contra Cheque');
    		}

	        $contracheque->update([
	            'TIPO' => TypeEnum::Create,
	            'SITUACAO' => SituationEnum::Pendent
	        ]);
    	}

        $this->warn("Criando Contra Cheque - [{$contracheque->NUMEMP} - {$contracheque->IDEXTERNO}]");

        $action = new CreatePaycheck();

        $response = $action->create($this->client(), $contracheque);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];

            if($contracheque->SITUACAO != SituationEnum::Success) {
                $update['SITUACAO'] = SituationEnum::Error;
            }

            $contracheque->update($update);

            return;
        }

        $contracheque->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => SituationEnum::Success,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);

        $contracheque->events()->update([
            'SITUACAO' => SituationEnum::Success
        ]);
    }

    /**
     * Busca Contra Cheque no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Paycheck>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Paycheck::isPendentToCreate()
                        ->get();
    }

    /**
     * @param \App\Models\Paycheck $contracheque
     * @return void
     **/
    protected function atualizaRegistro(Paycheck $contracheque): void
    {
        $this->info("Atualizando Contra Cheque [{$contracheque->NUMEMP} - {$contracheque->IDEXTERNO}]");

        try {
            $this->warn("Excluindo Contra Cheque - [{$contracheque->NUMEMP} - {$contracheque->IDEXTERNO}]");

            $this->deletaRegistro($contracheque);
        } catch(\Throwable) {
            $this->error('Erro ao excluir Contra Cheque');
        }

        $this->criaRegistro($contracheque);
    }

    /**
     * Busca Contra Cheque no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Paycheck>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Paycheck::isPendentToUpdate()
                    ->get();
    }

    /**
     * @param \App\Models\Paycheck $contracheque
     * @return void
     **/
    protected function deletaRegistro(Paycheck $contracheque): void
    {
        $this->info("Deletando Contra Cheque - [{$contracheque->IDEXTERNO} - {$contracheque->IDEXTERNO}]");

        $action = new DeletePaycheck();

        $response = $action->delete($this->client(), $contracheque);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];

            if($contracheque->SITUACAO != SituationEnum::Success) {
                $update['SITUACAO'] = SituationEnum::Error;
            }

            $contracheque->update($update);

            return;
        }

        $contracheque->update([
            'SITUACAO' => SituationEnum::Success,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Contra Cheque no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Paycheck>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return Paycheck::isPendentToDelete()
                    ->get();
    }
}