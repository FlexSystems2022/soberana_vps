<?php

namespace App\Action\AbsenceSituation;

use App\Action\NextiAction;
use App\Models\Absence\Situation;
use App\Shared\Provider\RestClient;

class CreateAbsenceSituation extends NextiAction
{
	/**
	 * Action Create Absence Situation
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Absence\Situation $situacao
	 * @return array
	 **/
	public function create(RestClient $client, Situation $situacao): array
	{
		try {
	        $payload = $this->makePayload($situacao);

	        $request = $client->post('absencesituations', options: [
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
	 * @param \App\Models\Absence\Situation $situacao
	 * @return array
	 **/
	private function makePayload(Situation $situacao): array
	{
		$payload = [
			'absenceTypeId' => $situacao->TIPOSIT ?? 1, // JUSTIFICADO
			'active' => true,
			'cid' => false,
			'deductWeeklyDayOff' => false,
			'externalId' => $situacao->IDEXTERNO,
			'initials' => null,
			'medicalDoctor' => false,
			'name' => $situacao->DESSIT,
			'removeAbsence' => false,
			'removeTemplate' => false,
			'requiredReplacement' => false,
			'showSituation' => false,
		];

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	}
}