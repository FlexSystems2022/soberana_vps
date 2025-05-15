<?php

namespace App\Action\WorkplaceTransfer;

use App\Action\NextiAction;
use App\Shared\Provider\RestClient;
use App\Models\Workplace\WorkplaceTransfer;

class DeleteWorkplaceTransfer extends NextiAction
{
	/**
	 * Action Delete Workplace Transfer
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Workplace\WorkplaceTransfer $troca
	 * @return array
	 **/
	public function delete(RestClient $client, WorkplaceTransfer $troca): array
	{
		try {
	        $request = $client->delete('workplacetransfers/' . $troca->ID);

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