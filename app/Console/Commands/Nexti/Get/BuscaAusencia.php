<?php

namespace App\Console\Commands\Nexti\Get;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Shared\Commands\CommandNexti;
use App\Action\Absence\GetLastUpdateAbsence;

class BuscaAusencia extends CommandNexti
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BuscaAusencia';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $today = today()->addDay();

        $date = $this->buscaMaxLastUpdate();

        $action = new GetLastUpdateAbsence();
        
        do {
            $finish = (clone $date)->addDays(1);
            
            $this->getData($action, $date, $finish);

            $date->addDays(1);
        } while (!$date->gte($today));

        return static::SUCCESS;
    }

    /**
     * Get Absenses by period date in nexti provider
     *
     * @param \App\Action\Absence\GetLastUpdateAbsence $action
     * @param \Illuminate\Support\Carbon $start
     * @param \Illuminate\Support\Carbon $finish
     * @return void
     **/
    private function getData(GetLastUpdateAbsence $action, Carbon $start, Carbon $finish): void
    {
        $page = 0;
        $total_page = 1;

        $this->warn("Processando periodo {$start->format('Y-m-d')} - {$finish->format('Y-m-d')}");

        do {
            $response = $action->get($this->client(), [
                'start' => $start,
                'finish' => $finish,
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
     * Busca Max Last Update
     * 
     * @return \Illuminate\Support\Carbon
     **/
    protected function buscaMaxLastUpdate(): Carbon
    {
        $found = DB::table('nexti_ret_ausencias')
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
     * @param array $ausencia
     * @return void
     **/
    protected function createOrUpdate(array $ausencia): void
    {
        $removed = $ausencia['removed'] ?? false;

        $data = [
            'ID' => $ausencia['id'],
            'LASTUPDATE' => $this->ajusteData($ausencia['lastUpdate'] ?? null, 'Y-m-d H:i:s'),
            'PERSONEXTERNALID' => $ausencia['personExternalId'] ?? null,
            'PERSONID' => $ausencia['personId'] ?? null,
            'NOTE' => $ausencia['note'] ?? null,
            'ABSENCESITUATIONEXTERNALID' => $ausencia['absenceSituationExternalId'] ?? null,
            'ABSENCESITUATIONID' => $ausencia['absenceSituationId'] ?? null,
            'FINISHDATETIME' => $this->ajusteData($ausencia['finishDateTime'] ?? null, 'Y-m-d H:i:s'),
            'STARTDATETIME' => $this->ajusteData($ausencia['startDateTime'] ?? null, 'Y-m-d H:i:s'),
            'REMOVED' => $removed,
            'USERREGISTERID' => $ausencia['userRegisterId'] ?? null,
            'CIDCODE' => $ausencia['cidCode'] ?? null,
            'CIDDESCRIPTION' => $ausencia['cidDescription'] ?? null,
            'CIDID' => $ausencia['cidId'] ?? null,
            'MEDICALDOCTORCRM' => $ausencia['medicalDoctorCrm'] ?? null,
            'MEDICALDOCTORID' => $ausencia['medicalDoctorId'] ?? null,
            'MEDICALDOCTORNAME' => $ausencia['medicalDoctorName'] ?? null,
            'FINISHMINUTE' => $ausencia['finishMinute'] ?? 0,
            'STARTMINUTE' => $ausencia['startMinute'] ?? 0,
            'TIPO' => $removed ? 3 : 0
        ];

        $model = DB::table('nexti_ret_ausencias')->where('ID', $data['ID']);

        $found = $model->first();
        if(!$found) {
            DB::table('nexti_ret_ausencias')->insert($data);

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