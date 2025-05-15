<?php

namespace App\Action\Union;

use App\Models\Union;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class CreateUnion extends NextiAction
{
	/**
	 * Action Create Union
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Union $sindicato
	 * @return array
	 **/
	public function create(RestClient $client, Union $sindicato): array
	{
		try {
	        $payload = $this->makePayload($sindicato);

	        $request = $client->post('tradeunions', options: [
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
	 * @param \App\Models\Union $sindicato
	 * @return array
	 **/
	private function makePayload(Union $sindicato): array
	{
		return [
			'active' => true,
            'externalId' => $sindicato->IDEXTERNO,
            'name' => $sindicato->NOMSIN,
            'registerDate' => date('dmYHis')
        ];
	} 
}