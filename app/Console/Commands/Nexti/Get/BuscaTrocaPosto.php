<?php

namespace App\Console\Commands\Nexti\Get;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Shared\Commands\CommandNexti;
use App\Action\WorkplaceTransfer\GetLastUpdateWorkplaceTransfer;

class BuscaTrocaPosto extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BuscaTrocaPosto';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $today = today()->addDay();

        $date = $this->buscaMaxLastUpdate();

        $action = new GetLastUpdateWorkplaceTransfer();
        
        do {
            $finish = (clone $date)->addDays(1);
            
            $this->getData($action, $date, $finish);

            $date->addDays(1);
        } while (!$date->gte($today));

        return static::SUCCESS;
    }

    /**
     * Get Schedule Transfer by period date in nexti provider
     *
     * @param \App\Action\WorkplaceTransfer\GetLastUpdateWorkplaceTransfer $action
     * @param \Illuminate\Support\Carbon $start
     * @param \Illuminate\Support\Carbon $finish
     * @return void
     **/
    private function getData(GetLastUpdateWorkplaceTransfer $action, Carbon $start, Carbon $finish): void
    {
        $page = 0;
        $total_page = 1;

        $this->warn("Processando periodo {$start->format('Y-m-d')} - {$finish->format('Y-m-d')}");

        do {
            $response = $action->get($this->client(), [
                'start' => $start,
                'finish' => $finish,
                'page' => $page,
                'size' => 100000,
            ]);

            if(isset($response['data'])) {
                foreach($response['data'] as $item) {
                    $this->createOrUpdate($item);
                }
            }
    
            $total_page = $response['total_page'];
            $page++;
            usleep(3000);
        } while ($page < $total_page);
    }

    /**
     * Busca Max Last Update
     * 
     * @return \Illuminate\Support\Carbon
     **/
    protected function buscaMaxLastUpdate(): Carbon
    {
        $found = DB::table('NEXTI_RET_TROCA_POSTO')
                    ->selectRaw('MAX(LASTUPDATE) as LASTUPDATE')
                    ->first();

        if ($found->LASTUPDATE) {
            return Carbon::parse($found->LASTUPDATE)->subDays(1);
        }

        return today()->startOfYear();
    }

    /**
     * Cria ou Atualiza
     * 
     * @param array $troca
     * @return void
     **/
    protected function createOrUpdate(array $troca): void
    {
        var_dump($troca);
        $data = [
            'ID' => $troca['id'],
            'LASTUPDATE' => $this->ajusteData($troca['lastUpdate'] ?? null, 'Y-m-d H:i:s'),
            'PERSONEXTERNALID' => $troca['personExternalId'] ?? null,
            'PERSONID' => $troca['personId'] ?? null,
            'TRANSFERDATETIME' => $this->ajusteData($troca['transferDateTime'] ?? null, 'Y-m-d H:i:s'),
            'WORKPLACEID' => $troca['workplaceId'] ?? null,
            'WORKPLACEEXTERNALID' => $troca['workplaceExternalId'] ?? null,
            'REMOVED' => $troca['removed'] == true ? 1 : 0,
            'TIPO' => $troca['removed'] == true ? 3 : 0,
            'USERREGISTERID' => $troca['userRegisterId'] ?? null
        ];

        $model = DB::table('NEXTI_RET_TROCA_POSTO')->where('ID', $data['ID']);

        $found = $model->first();
        if(!$found) {
            DB::table('NEXTI_RET_TROCA_POSTO')->insert($data);

            $this->info("Criado {$data['ID']}");

            return;
        }

        $model->update($data);

        $this->info("Atualizado {$data['ID']}");
    }

    /**
     * Ajusta data
     * 
     * @param string|null $date
     * @param string $format
     * @return string|null
     **/
    private function ajusteData(string|null $date, string $format): string|null
    {
        if(!$date) {
            return null;
        }

        return Carbon::createFromFormat('dmYHis', $date)->format($format);
    }
}