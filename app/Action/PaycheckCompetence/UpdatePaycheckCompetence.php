<?php

namespace App\Action\PaycheckCompetence;

use App\Action\NextiAction;
use App\Shared\Provider\RestClient;
use App\Models\Paycheck\Competence;
use Illuminate\Support\Facades\Date;

class UpdatePaycheckCompetence extends NextiAction
{
	/**
	 * Action Update Paycheck Competence
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Paycheck\Competence $competencia
	 * @return array
	 **/
	public function update(RestClient $client, Competence $competencia): array
	{
		try {
	        $payload = $this->makePayload($competencia);

	        $request = $client->put('paycheckperiods/' . $competencia->ID, options: [
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
	 * @param \App\Models\Paycheck\Competence $competencia
	 * @return array
	 **/
	private function makePayload(Competence $competencia): array
	{
		$DATPAG = $competencia->DATPAG ? Date::parse($competencia->DATPAG) : null;
		$PAYCHECKPERIODDATE = $competencia->PAYCHECKPERIODDATE ? Date::parse($competencia->PAYCHECKPERIODDATE) : null;

		$payload = [
			'exhibitionDate' => $DATPAG->format('dmYHis'),
			'name' => $competencia->NAME,
			'paycheckPeriodDate' => $PAYCHECKPERIODDATE->format('dmY'),
			'externalId' => $competencia->IDEXTERNO,
        ];

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}