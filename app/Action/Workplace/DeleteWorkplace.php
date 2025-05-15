<?php

namespace App\Action\Workplace;

use App\Models\Workplace;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class DeleteWorkplace extends NextiAction
{
	/**
	 * Action Delete Workplace
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Workplace $posto
	 * @return array
	 **/
	public function delete(RestClient $client, Workplace $posto): array
	{
		try {
	        $request = $client->delete('workplaces/' . $posto->ID);

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