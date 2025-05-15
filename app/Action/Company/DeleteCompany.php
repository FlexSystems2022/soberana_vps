<?php

namespace App\Action\Company;

use App\Models\Company;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class DeleteCompany extends NextiAction
{
	/**
	 * Action Delete Company
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Company $empresa
	 * @return array
	 **/
	public function delete(RestClient $client, Company $empresa): array
	{
		try {
	        $request = $client->delete('companies/' . $empresa->ID);

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