<?php

namespace App\Action\Client;

use App\Models\Client;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class CreateClient extends NextiAction
{
	/**
	 * Action Create Client
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Client $cliente
	 * @return array
	 **/
	public function create(RestClient $client, Client $cliente): array
	{
		try {
	        $payload = $this->makePayload($cliente);

	        $request = $client->post('clients', options: [
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
	 * @param \App\Models\Client $cliente
	 * @return array
	 **/
	private function makePayload(Client $cliente): array
	{
		return [
	        'active' => true,
	        'externalId' => $cliente->IDEXTERNO,
	        'name' => $cliente->NOMOEM,
	        'premarkedInterval' => false,
        ];
	} 
}