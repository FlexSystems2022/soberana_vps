<?php

namespace App\Action\WorkplaceTransfer;

use App\Action\NextiAction;
use Illuminate\Support\Carbon;
use App\Shared\Provider\RestClient;

class GetLastUpdateWorkplaceTransfer extends NextiAction
{
	/**
	 * Action Get Workplace Transfer
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param array $fields
	 * @return array
	 **/
	public function get(RestClient $client, array $fields): array
	{
		try {
	        $payload = $this->makePayload($fields);

	        $request = $client->get("workplacetransfers/lastupdate/start/{$payload['start']}/finish/{$payload['finish']}",
	        	query: [
		            'page' => $payload['page'],
		            'size' => $payload['size'],
	    	    ]
		    );

	        $response = [
	            'data' => $request->getResponseAsJson(),
	            'code' => $request->getResponse()->getStatusCode()
	        ];

	        $this->throwError($response, $payload);

			return [
				'success' => true,
		        'total_page' => $response['data']['totalPages'] ?? 0,
				'data' => $response['data']['content']
			];
		} catch(\Throwable $e) {
            $message = $e->getMessage();

            if($e->getCode() === 401) {
                $message = __('nexti.invalid.auth');
            }

            return [
				'success' => false,
		        'total_page' => $response['data']['totalPages'] ?? 0,
		        'data' => []
            ];
        }
	}

	/**
	 * Make Payload
	 * 
	 * @param object $fields
	 * @return array
	 **/
	private function makePayload(array $fields): array
	{
		return [
	        'start' => Carbon::create($fields['start'])->format('dmYHis'),
	        'finish' => Carbon::create($fields['finish'])->format('dmY') . '235959',
	        'page' => $fields['page'] ?? 1,
	        'size' => $fields['size'] ?? 10000,
	    ];
	} 
}