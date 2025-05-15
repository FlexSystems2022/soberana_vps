<?php

namespace App\Action\People;

use App\Models\People;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class DeletePeople extends NextiAction
{
	/**
	 * Action Delete People
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\People $colaborador
	 * @return array
	 **/
	public function delete(RestClient $client, People $colaborador): array
	{
		try {
	        $request = $client->delete('persons/' . $colaborador->ID);

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