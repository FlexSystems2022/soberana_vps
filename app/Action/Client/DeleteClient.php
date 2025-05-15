<?php

namespace App\Action\Client;

use App\Models\Client;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class DeleteClient extends NextiAction
{
	/**
	 * Action Delete Client
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Client $cliente
	 * @return array
	 **/
	public function delete(RestClient $client, Client $cliente): array
	{
		try {
	        $request = $client->delete('clients/' . $cliente->ID);

	        $response = [
	            'data' => $request->getResponseAsJson(),
	            'code' => $request->getResponse()->getStatusCode()
	        ];

	        $this->throwError($response, []);

			return [
				'success' => true
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
}