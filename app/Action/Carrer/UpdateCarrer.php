<?php

namespace App\Action\Carrer;

use App\Models\Career;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class UpdateCarrer extends NextiAction
{
	/**
	 * Action Update Carrer
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Career $carrer
	 * @return array
	 **/
	public function update(RestClient $client, Career $cargo): array
	{
		try {
	        $payload = $this->makePayload($cargo);

	        $request = $client->put('careers/' . $cargo->ID, options: [
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
	 * @param \App\Models\Career $cargo
	 * @return array
	 **/
	private function makePayload(Career $cargo): array
	{
		return [
            'externalId' => $cargo->IDEXTERNO,
            'name' => $cargo->TITCAR,
        ];
	} 
}