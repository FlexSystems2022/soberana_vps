<?php

namespace App\Action\Workplace;

use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class GetAllWorkplace extends NextiAction
{
	/**
	 * Action Get Workplace
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param array $fields
	 * @return array
	 **/
	public function get(RestClient $client, array $fields): array
	{
		try {
	        $payload = $this->makePayload($fields);

	        $request = $client->get('workplaces/all',
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
	        'page' => $fields['page'] ?? 1,
	        'size' => $fields['size'] ?? 10000,
	    ];
	} 
}