<?php

namespace App\Action\ScheduleTransfer;

use App\Action\NextiAction;
use App\Shared\Provider\RestClient;
use App\Models\Schedule\ScheduleTransfer;

class CreateScheduleTransfer extends NextiAction
{
	/**
	 * Action Create Schedule Transfer
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Schedule\ScheduleTransfer $troca
	 * @return array
	 **/
	public function create(RestClient $client, ScheduleTransfer $troca): array
	{
		try {
	        $payload = $this->makePayload($troca);

	        $request = $client->post('scheduletransfers', options: [
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
	 * @param \App\Models\Schedule\ScheduleTransfer $troca
	 * @return array
	 **/
	private function makePayload(ScheduleTransfer $troca): array
	{
		$payload = [
			'personExternalId' => $troca->people->IDEXTERNO,
			'personId' => $troca->people->ID,
			'removed' => false,
			'scheduleId' => $troca->ESCALA,
			'rotationCode' => $troca->TURMA,
			'transferDateTime' => $troca->DATALT?->format('dmYHis'),
        ];

        if(!$payload['personId'] && !$payload['personExternalId']) {
        	throw new \Exception('Colaborador não encontrado na nexti!');
        }

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}