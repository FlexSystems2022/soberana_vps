<?php

namespace App\Action\Workplace;

use App\Models\Workplace;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;
use Illuminate\Support\Facades\Date;

class UpdateWorkplace extends NextiAction
{
	/**
	 * Action Update Workplace
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Workplace $posto
	 * @return array
	 **/
	public function update(RestClient $client, Workplace $posto): array
	{
		try {
	        $payload = $this->makePayload($posto);

	        $request = $client->put('workplaces/' . $posto->ID, options: [
	            'json' => $payload
	        ]);

	        $response = [
	            'data' => $request->getResponseAsJson(),
	            'code' => $request->getResponse()->getStatusCode()
	        ];

	        $this->throwError($response, $payload);

			return [
				'success' => true,
				'data' => $response['data']
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

	/**
	 * Make Payload
	 * 
	 * @param \App\Models\Workplace $posto
	 * @return array
	 **/
	private function makePayload(Workplace $posto): array
	{
		$payload = [
			'active' => $posto->TIPO !== 3,
			'forceCompanyTransfer' => true,
			'timezone' => 'America/Sao_Paulo',
			'externalClientId' => $posto->IDEXTERNOCLIENTE,
			'companyId' => $posto->IDEMPRESA,
			'externalId' => $posto->IDEXTERNO,
			'name' => $posto->DESPOS,
			'serviceTypeId' => $posto->TIPO_SERVICO,
			'service' => $posto->SERVICO,
			'vacantJob' => $posto->VAGAS,
			'startDate' => Date::parse($posto->DATCRI)->format('dmY000000'),
			'costCenter' => $posto->CODCCU,
			'workplaceNumberTxt' => $posto->POSTRA,
			'companyNumber' => $posto->CPFCGC,
			'companyName' => $posto->RAZAOSOCIAL,
			'businessUnitId' => $posto->UNIDADE_NEGOCIO,
        ];

        if ($posto->DATEXT && $posto->DATEXT !== '1900-12-31') {
            $payload['finishDate'] = Date::parse($posto->DATEXT)->format('dmY000000');
        }

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}