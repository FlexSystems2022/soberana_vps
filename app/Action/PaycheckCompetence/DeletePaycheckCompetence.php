<?php

namespace App\Action\PaycheckCompetence;

use App\Action\NextiAction;
use App\Shared\Provider\RestClient;
use App\Models\Paycheck\Competence;

class DeletePaycheckCompetence extends NextiAction
{
	/**
	 * Action Delete Paycheck Competence
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Paycheck\Competence $competencia
	 * @return array
	 **/
	public function delete(RestClient $client, Competence $competencia): array
	{
		try {
	        $request = $client->delete('paycheckperiods/externalid/' . $competencia->IDEXTERNO);

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