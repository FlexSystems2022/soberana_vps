<?php

namespace App\Action\Paycheck;

use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class DeletePaycheck extends NextiAction
{
	/**
	 * Action Delete Paycheck
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param object $contracheque
	 * @return array
	 **/
	public function delete(RestClient $client, object $contracheque): array
	{
		try {
	        $request = $client->delete('paychecks/externalid/' . $contracheque->IDEXTERNO);

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