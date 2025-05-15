<?php

namespace App\Console\Commands\Nexti\Get;

use Illuminate\Support\Facades\DB;
use App\Shared\Commands\CommandNexti;
use App\Action\Workplace\GetAllWorkplace;

class BuscaPosto extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BuscaPosto';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $action = new GetAllWorkplace();
        
        $this->getData($action);

        return static::SUCCESS;
    }

    /**
     * Get Schedule Transfer by period date in nexti provider
     *
     * @param \App\Action\Workplace\GetAllWorkplace $action
     * @return void
     **/
    private function getData(GetAllWorkplace $action): void
    {
        $page = 0;
        $total_page = 1;

        $this->warn("Buscando Postos");

        do {
            $response = $action->get($this->client(), [
                'page' => $page,
                'size' => 1000,
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
     * Cria ou Atualiza
     * 
     * @param array $posto
     * @return void
     **/
    protected function createOrUpdate(array $posto): void
    {
        $data = [
            'ID' => $posto['id'],
            'NAME' => $posto['name'],
            'EXTERNALID' => $posto['externalId'] ?? null,
        ];

        $model = DB::table('NEXTI_RET_POSTO')->where('ID', $data['ID']);

        $found = $model->first();
        if(!$found) {
            DB::table('NEXTI_RET_POSTO')->insert($data);

            $this->info("Criado {$data['ID']}");

            return;
        }

        $model->update($data);

        $this->info("Atualizado {$data['ID']}");
    }
}