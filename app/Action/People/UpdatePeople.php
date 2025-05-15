<?php

namespace App\Action\People;

use App\Models\People;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class UpdatePeople extends NextiAction
{
	/**
	 * Action Update People
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\People $colaborador
	 * @return array
	 **/
	public function update(RestClient $client, People $colaborador): array
	{
		try {
	        $payload = $this->makePayload($colaborador);

	        $request = $client->put('persons/' . $colaborador->ID, options: [
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
	 * @param \App\Models\People $colaborador
	 * @return array
	 **/
	private function makePayload(People $colaborador): array
	{
		$payload = [
	        'address' => $colaborador->ENDERECO,
	        'admissionDate' => $colaborador->DATADM?->format('dmY000000'),
	        'birthDate' => $colaborador->DATANASC?->format('dmY000000'),
            'careerId' => $colaborador->career?->ID,
            'externalCareerId' => $colaborador->career?->IDEXTERNO,
            'companyId' => $colaborador->company?->ID,
            'externalCompanyId' => $colaborador->company?->IDEXTERNO,
	        'cpf' => $colaborador->CPF,
	        'externalId' => $colaborador->IDEXTERNO,
	        'district' => $colaborador->BAIRRO,
	        'email' => $colaborador->EMAIL,
	        'houseNumber' => $colaborador->NUMERO,
	        'ignoreValidation' => $colaborador->IGNOREVALIDATION == 1,
	        'name' => $colaborador->NOMFUN,
	        'phone' => $colaborador->TELEFONE,
	        'phone2' => $colaborador->CELULAR,
	        //'pis' => $colaborador->PIS,
	        'enrolment' => $colaborador->NUMCAD,
	        'personSituationId' => $colaborador->SITFUN ?? 1,
	        'demissionDate' => $colaborador->DATADEM?->format('dmY000000'),
	        'personTypeId' => $colaborador->TIPCOL,
        ];

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}