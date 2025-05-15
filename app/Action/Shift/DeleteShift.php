<?php

namespace App\Action\Shift;

use App\Models\Shift;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class DeleteShift extends NextiAction
{
	/**
	 * Action Delete Shift
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Shift $horario
	 * @return array
	 **/
	public function delete(RestClient $client, Shift $horario): array
	{
		try {
	        $request = $client->delete('shifts/' . $horario->ID);

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