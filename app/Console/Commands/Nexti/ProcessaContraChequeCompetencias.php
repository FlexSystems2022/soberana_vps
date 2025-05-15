<?php

namespace App\Console\Commands\Nexti;

use App\Shared\Enums\TypeEnum;
use Illuminate\Support\Collection;
use App\Shared\Enums\SituationEnum;
use App\Models\Paycheck\Competence;
use App\Shared\Commands\CommandNexti;
use App\Action\PaycheckCompetence\CreatePaycheckCompetence;
use App\Action\PaycheckCompetence\UpdatePaycheckCompetence;
use App\Action\PaycheckCompetence\DeletePaycheckCompetence;

class ProcessaContraChequeCompetencias extends CommandNexti
{
    /**
     * @var bool
     */
    protected bool $log = true;

    /**
     * Request timeinterval in seconds
     */
    protected float $timeInterval = 0;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ProcessaContraChequeCompetencias';

    /**
     * @param \App\Models\Paycheck\Competence $competence
     * @return void
     **/
    protected function criaRegistro(Competence $competence): void
    {
        if($competence->SITUACAO == SituationEnum::Error) {
            try {
                $this->warn("Excluindo Competencia - [{$competence->NUMEMP} - {$competence->IDEXTERNO}]");

                $this->deletaRegistro($competence);
            } catch(\Throwable) {
                $this->error('Erro ao excluir Competencia');
            }

            $competence->update([
                'TIPO' => TypeEnum::Create,
                'SITUACAO' => SituationEnum::Pendent
            ]);
        }

        $this->warn("Criando Competencia - [{$competence->NUMEMP} - {$competence->IDEXTERNO}]");

        $action = new CreatePaycheckCompetence();

        $response = $action->create($this->client(), $competence);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];

            if($competence->SITUACAO != SituationEnum::Success) {
                $update['SITUACAO'] = SituationEnum::Error;
            }

            $competence->update($update);

            return;
        }

        $competence->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => SituationEnum::Success,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Competencia no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Paycheck\Competence>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Competence::isPendentToCreate()
                        ->get();
    }

    /**
     * @param \App\Models\Paycheck\Competence $competence
     * @return mixed
     **/
    protected function atualizaRegistro(Competence $competence): void
    {
        $this->info("Atualizando Competencia [{$competence->NUMEMP} - {$competence->IDEXTERNO}]");

        $action = new UpdatePaycheckCompetence();

        $response = $action->update($this->client(), $competence);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];

            if($competence->SITUACAO != SituationEnum::Success) {
                $update['SITUACAO'] = SituationEnum::Error;
            }

            $competence->update($update);

            return;
        }

        $competence->update([
            'SITUACAO' => SituationEnum::Success,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Competencia no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Paycheck\Competence>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Competence::isPendentToUpdate()
                    ->get();
    }

    /**
     * @param \App\Models\Paycheck\Competence $competence
     * @return void
     **/
    protected function deletaRegistro(Competence $competence): void
    {
        $this->info("Deletando Competencia - [{$competence->IDEXTERNO} - {$competence->IDEXTERNO}]");

        $action = new DeletePaycheckCompetence();

        $response = $action->delete($this->client(), $competence);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($competence->SITUACAO != SituationEnum::Success) {
                $update['SITUACAO'] = SituationEnum::Error;
            }

            $competence->update($update);

            return;
        }

        $competence->update([
            'SITUACAO' => SituationEnum::Success,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Competencia no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Paycheck\Competence>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return Competence::isPendentToDelete()
                    ->get();
    }
}