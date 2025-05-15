<?php

namespace App\Action\PaycheckCompetence;

use App\Action\NextiAction;
use App\Models\Paycheck\Competence;
use App\Shared\Provider\RestClient;
use Illuminate\Support\Facades\Date;

class CreatePaycheckCompetence extends NextiAction
{
	/**
	 * Action Create Paycheck Competence
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Paycheck\Competence $competencia
	 * @return array
	 **/
	public function create(RestClient $client, Competence $competencia): array
	{
		try {
	        $payload = $this->makePayload($competencia);

	        $request = $client->post('paycheckperiods', options: [
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
	 * @param \App\Models\Paycheck\Competence $competencia
	 * @return array
	 **/
	private function makePayload(Competence $competencia): array
	{
		$DATPAG = $competencia->DATPAG ? Date::parse($competencia->DATPAG) : null;
		$PAYCHECKPERIODDATE = $competencia->PAYCHECKPERIODDATE ? Date::parse($competencia->PAYCHECKPERIODDATE) : null;

		$payload = [
			'companyId' => $competencia->company->ID,
			'exhibitionDate' => $DATPAG->format('dmYHis'),
			'externalCompanyId' => $competencia->company->IDEXTERNO,
			'externalId' => $competencia->IDEXTERNO,
			'name' => $competencia->NAME,
			'paycheckPeriodDate' => $PAYCHECKPERIODDATE->format('dmY'),
        ];

        if(!$payload['companyId'] || !$payload['externalCompanyId']) {
        	throw new \Exception('Empresa não encontrado na nexti!');
        }

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}