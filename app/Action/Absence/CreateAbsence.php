<?php

namespace App\Action\Absence;

use App\Shared\Helper;
use App\Models\Absence;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;
use Illuminate\Support\Facades\Date;

class CreateAbsence extends NextiAction
{
	/**
	 * Action Create Absence
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Absence $absence
	 * @return array
	 **/
	public function create(RestClient $client, Absence $absence): array
	{
		try {
	        $payload = $this->makePayload($absence);

	        $request = $client->post('absences', options: [
	            'json' => $payload
	        ]);

	        $response = [
	            'data' => $request->getResponseAsJson(),
	            'code' => $request->getResponse()->getStatusCode()
	        ];

	        $this->throwError($response, $payload);

			return [
				'success' => true,
				'data' => $response['data']['value']
			];
		} catch(\Throwable $e) {
            $message = $e->getMessage();

            if($e->getCode() === 401) {
                $message = __('nexti.invalid.auth');
            }

            return [
				'success' => false,
				'message' => $message
            ];
        }
	}

	/**
	 * Make Payload
	 * 
	 * @param \App\Models\Absence $absence
	 * @return array
	 **/
	private function makePayload(Absence $absence): array
	{
		$payload = [
			'absenceSituationExternalId' => $absence->situation?->IDEXTERNO,
			'finishDateTime' => $absence->FINISHDATETIME?->format('dmY000000') ?? null,
			'finishMinute' => Helper::hourConvertToMinutes($absence->FINISHDATETIME?->format('H:i')),
			'note' => $absence->OBSAFA,
	        'personId' => $absence->people->ID,
	        'personExternalId' => $absence->people->IDEXTERNO,
			'startDateTime' => $absence->STARTDATETIME?->format('dmY000000') ?? null,
			'startMinute' => Helper::hourConvertToMinutes($absence->STARTDATETIME?->format('H:i'))
        ];

        $payload['finishMinute'] = $payload['finishMinute'] == 0 ? null : $payload['finishMinute'];
        $payload['startMinute'] = $payload['startMinute'] == 0 ? null : $payload['startMinute'];

        if(!$payload['personExternalId']) {
        	throw new \Exception('Colaborador não encontrado na nexti!');
        }

        if(!$payload['absenceSituationExternalId']) {
        	throw new \Exception('Situação não encontrada na nexti!');
        }

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}