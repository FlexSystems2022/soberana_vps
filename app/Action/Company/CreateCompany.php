<?php

namespace App\Action\Company;

use App\Models\Company;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class CreateCompany extends NextiAction
{
	/**
	 * Action Create Company
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Company $empresa
	 * @return array
	 **/
	public function create(RestClient $client, Company $empresa): array
	{
		try {
	        $payload = $this->makePayload($empresa);

	        $request = $client->post('companies', options: [
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
	 * @param \App\Models\Company $empresa
	 * @return array
	 **/
	private function makePayload(Company $empresa): array
	{
		return [
	        'active' => true,
	        'externalId' => $empresa->IDEXTERNO,
	        'companyName' => $empresa->RAZSOC,
	        'companyNumber' => $empresa->CNPJ,
	        'companyNumberType' => str($empresa->CNPJ)->length() > 11 ? 'CNPJ' : 'CPF',
	        'fantasyName' => $empresa->NOMFIL ?? null,
        ];
	} 
}