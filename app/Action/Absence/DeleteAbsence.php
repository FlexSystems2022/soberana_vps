<?php

namespace App\Action\Absence;

use App\Models\Absence;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class DeleteAbsence extends NextiAction
{
	/**
	 * Action Delete Absence
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Absence $absence
	 * @return array
	 **/
	public function delete(RestClient $client, Absence $absence): array
	{
		try {
	        $request = $client->delete('absences/' . $absence->ID);

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