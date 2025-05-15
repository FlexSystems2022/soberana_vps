<?php

namespace App\Action\Carrer;

use App\Models\Career;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class DeleteCarrer extends NextiAction
{
	/**
	 * Action Delete Carrer
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Career $carrer
	 * @return array
	 **/
	public function delete(RestClient $client, Career $cargo): array
	{
		try {
	        $request = $client->delete('careers/' . $cargo->ID);

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