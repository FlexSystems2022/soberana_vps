<?php

namespace App\Action\ScheduleTransfer;

use App\Action\NextiAction;
use App\Shared\Provider\RestClient;
use App\Models\Schedule\ScheduleTransfer;

class DeleteScheduleTransfer extends NextiAction
{
	/**
	 * Action Delete Schedule Transfer
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Schedule\ScheduleTransfer $troca
	 * @return array
	 **/
	public function delete(RestClient $client, ScheduleTransfer $troca): array
	{
		try {
	        $request = $client->delete('scheduletransfers/' . $troca->ID);

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