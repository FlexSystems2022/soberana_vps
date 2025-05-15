<?php

namespace App\Action\AbsenceSituation;

use App\Action\NextiAction;
use App\Models\Absence\Situation;
use App\Shared\Provider\RestClient;

class UpdateAbsenceSituation extends NextiAction
{
	/**
	 * Action Update Absence Situation
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Absence\Situation $situacao
	 * @return array
	 **/
	public function update(RestClient $client, Situation $situacao): array
	{
		try {
	        $payload = $this->makePayload($situacao);

	        $request = $client->put('absencesituations/' . $situacao->ID, options: [
	            'json' => $payload
	        ]);

	        $response = [
	            'data' => $request->getResponseAsJson(),
	            'code' => $request->getResponse()->getStatusCode()
	        ];

	        $this->throwError($response, $payload);

			return [
				'success' => true,
				'data' => $response['data']
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
	 * @param \App\Models\Absence\Situation $situacao
	 * @return array
	 **/
	private function makePayload(Situation $situacao): array
	{
		$payload = [
			'absenceTypeId' => $situacao->TIPOSIT ?? 1, // JUSTIFICADO
			'active' => true,
			'externalId' => $situacao->IDEXTERNO,
			'name' => $situacao->DESSIT,
        ];

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}