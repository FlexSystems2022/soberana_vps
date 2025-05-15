<?php

namespace App\Console\Commands\Nexti;

use App\Models\Workplace;
use Illuminate\Support\Collection;
use App\Shared\Commands\CommandNexti;
use App\Action\Workplace\CreateWorkplace;
use App\Action\Workplace\DeleteWorkplace;
use App\Action\Workplace\UpdateWorkplace;
use Illuminate\Database\Query\JoinClause;

class ProcessaPosto extends CommandNexti
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
    protected $signature = 'ProcessaPosto';

    /**
     * @param \App\Models\Workplace $posto
     * @return mixed
     **/
    protected function criaRegistro(Workplace $posto): void
    {
        $this->info("Criando Posto {$posto->DESPOS}");

        $action = new CreateWorkplace();

        $response = $action->create($this->client(), $posto);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($posto->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $posto->update($update);

            return;
        }

        $posto->update([
            'ID' => $response['data']['id'],
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Criado na Nexti'
        ]);
    }

    /**
     * Busca Postos no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Workplace>
     **/
    protected function buscaRegistrosCriar(): Collection
    {
        return Workplace::query()
                    ->select('NEXTI_POSTO.*')
                    ->selectRaw('NEXTI_CLIENTE.IDEXTERNO as IDEXTERNOCLIENTE')
                    ->selectRaw('NEXTI_EMPRESA.ID as IDEMPRESA')
                    ->leftJoin('NEXTI_CLIENTE',
                        fn(JoinClause $join) => $join->on('NEXTI_CLIENTE.CODOEM', 'NEXTI_POSTO.CODOEM')
                                                    ->whereColumn('NEXTI_CLIENTE.NUMEMP', 'NEXTI_POSTO.NUMEMP')
                    )
                    ->leftJoin('NEXTI_EMPRESA',
                        fn(JoinClause $join) => $join->on('NEXTI_EMPRESA.NUMEMP', 'NEXTI_POSTO.NUMEMP')
                                                    ->whereColumn('NEXTI_EMPRESA.CODFIL', 'NEXTI_POSTO.CODFIL')
                    )
                    ->where('NEXTI_POSTO.TIPO', 0)
                    ->whereIn('NEXTI_POSTO.SITUACAO', [0, 2])
                    ->get();
    }

    /**
     * @param \App\Models\Workplace $posto
     * @return mixed
     **/
    protected function atualizaRegistro(Workplace $posto): void
    {
        $this->info("Atualizando Posto {$posto->DESPOS}");

        $action = new UpdateWorkplace();

        $response = $action->update($this->client(), $posto);

        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($posto->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $posto->update($update);

            return;
        }

        $posto->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Atualizado na Nexti'
        ]);
    }

    /**
     * Busca Postos no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Workplace>
     **/
    protected function buscaRegistrosAtualizar(): Collection
    {
        return Workplace::query()
                    ->select('NEXTI_POSTO.*')
                    ->selectRaw('NEXTI_CLIENTE.IDEXTERNO as IDEXTERNOCLIENTE')
                    ->selectRaw('NEXTI_EMPRESA.ID as IDEMPRESA')
                    ->leftJoin('NEXTI_CLIENTE',
                        fn(JoinClause $join) => $join->on('NEXTI_CLIENTE.CODOEM', 'NEXTI_POSTO.CODOEM')
                                                    ->whereColumn('NEXTI_CLIENTE.NUMEMP', 'NEXTI_POSTO.NUMEMP')
                    )
                    ->leftJoin('NEXTI_EMPRESA',
                        fn(JoinClause $join) => $join->on('NEXTI_EMPRESA.NUMEMP', 'NEXTI_POSTO.NUMEMP')
                                                    ->whereColumn('NEXTI_EMPRESA.CODFIL', 'NEXTI_POSTO.CODFIL')
                    )
                    ->where('NEXTI_POSTO.TIPO', 1)
                    ->whereIn('NEXTI_POSTO.SITUACAO', [0, 2])
                    ->get();
    }

    /**
     * @param \App\Models\Workplace $posto
     * @return mixed
     **/
    protected function deletaRegistro(Workplace $posto): void
    {
        $this->info("Excluindo Posto {$posto->DESPOS}");

        $action = new DeleteWorkplace();

        $response = $action->delete($this->client(), $posto);
        if(!$response['success']) {
            $this->error("Result - [{$response['message']}]");

            $update = [
                'OBSERVACAO' => date('Y-m-d H:i:s') . ' - ' . $response['message'],
            ];
            if($posto->SITUACAO != 1) {
                $update['SITUACAO'] = 2;
            }

            $posto->update($update);
            return;
        }

        $posto->update([
            'SITUACAO' => 1,
            'OBSERVACAO' => date('Y-m-d H:i:s') . ' - Deletado na Nexti'
        ]);
    }

    /**
     * Busca Posto no banco
     * 
     * @return \Illuminate\Support\Collection<\App\Models\Workplace>
     **/
    protected function buscaRegistrosDeletar(): Collection
    {
        return Workplace::isPendentToDelete()
                    ->get();
    }
}