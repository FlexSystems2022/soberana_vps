<?php

namespace App\Action\WorkplaceTransfer;

use App\Action\NextiAction;
use App\Shared\Provider\RestClient;
use App\Models\Workplace\WorkplaceTransfer;

class CreateWorkplaceTransfer extends NextiAction
{
	/**
	 * Action Create Workplace Transfer
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Workplace\WorkplaceTransfer $troca
	 * @return array
	 **/
	public function create(RestClient $client, WorkplaceTransfer $troca): array
	{
		try {
	        $payload = $this->makePayload($troca);

	        $request = $client->post('workplacetransfers', options: [
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
	 * @param \App\Models\Workplace\WorkplaceTransfer $troca
	 * @return array
	 **/
	private function makePayload(WorkplaceTransfer $troca): array
	{
		$payload = [
	        'removed' => false,
	        'transferDateTime' => $troca->INIATU?->format('dmY000000'),
	        'personId' => $troca->people?->ID,
	        'personExternalId' => $troca->people?->IDEXTERNO,
	        'workplaceId' => $troca->workplace?->ID,
	        'workplaceExternalId' => $troca->workplace?->IDEXTERNO,
        ];

        if(!$payload['personExternalId']) {
        	throw new \Exception('Colaborador não encontrado na nexti!');
        }

        if(!$payload['workplaceExternalId']) {
        	throw new \Exception('Posto não encontrada na nexti!');
        }

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}