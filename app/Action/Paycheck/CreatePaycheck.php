<?php

namespace App\Action\Paycheck;

use App\Action\NextiAction;
use App\Models\Paycheck\Event;
use App\Shared\Provider\RestClient;
use Illuminate\Support\Facades\Date;

class CreatePaycheck extends NextiAction
{
	/**
	 * Action Create Paycheck Competence
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param object $contracheque
	 * @return array
	 **/
	public function create(RestClient $client, object $contracheque): array
	{
		try {
	        $payload = $this->makePayload($contracheque);

	        $request = $client->post('paychecks', options: [
	            'json' => $payload
	        ]);

	        $response = [
	            'data' => $request->getResponseAsJson(),
	            'code' => $request->getResponse()->getStatusCode()
	        ];

	        $this->throwError($response, $payload);

			return [
				'success' => true,
				'data' => $response['data']['value']
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
	 * @param object $contracheque
	 * @return array
	 **/
	private function makePayload(object $contracheque): array
	{
        if(!$contracheque->competence || !$contracheque->competence->ID) {
        	throw new \Exception('Competencia não encontrado na nexti!');
        }

		$contracheque->competence->PAYCHECKPERIODDATE = $contracheque->competence->PAYCHECKPERIODDATE ?
															Date::parse($contracheque->competence->PAYCHECKPERIODDATE) :
															null;

		$payload = [
			'baseFgts' => floatval($contracheque->BASEFGTS),
			'baseInss' => floatval($contracheque->BASEINSS),
			'baseIrrf' => 0,
			'companyExternalId' => $contracheque->competence->company->IDEXTERNO,
			'companyId' => $contracheque->competence->company->ID,
			'externalId' => $contracheque->IDEXTERNO,
			'externalPaycheckPeriodId' => $contracheque->CONTRA_CHEQUE_CMP,
			'grossPay' => floatval($contracheque->GROSSPAY),
			'monthFgts' => floatval($contracheque->MONTHFGTS),
			'note' => null,
			'paycheckPeriodDate' => $contracheque->competence->PAYCHECKPERIODDATE?->format('Y-m-d'),
			'paycheckPeriodId' => $contracheque->competence->ID,
			'paycheckPeriodName' => $contracheque->competence->NAME,
			'paycheckRecords' => [],
			'personEnrolment' => $contracheque->people?->NUMCAD,
			'personExternalId' => $contracheque->people?->IDEXTERNO,
			'personId' => $contracheque->people?->ID,
			'totalExpense' => 0,
			'totalRevenue' => 0,
			'netPay' => 0
        ];

		$contracheque->events->each(function(Event $event) use(&$payload) {
			$item = [
				'cost' => floatval($event->COST),
				'description' => $event->DESCRIPTION,
				'paycheckRecordTypeId' => $event->PAYCHECKRECORDTYPEID,
				'reference' => $event->REFERENCE,
			];

			$payload['paycheckRecords'][] = $item;

			if($item['paycheckRecordTypeId'] === 2) {
				$payload['totalExpense'] += $item['cost'];
			} else {
				$payload['totalRevenue'] += $item['cost'];
			}
		});

		$payload['netPay'] = $payload['totalRevenue'] - $payload['totalExpense'];

        if(!$payload['companyId'] || !$payload['companyExternalId']) {
        	throw new \Exception('Empresa não encontrado na nexti!');
        }

        if(!$payload['personExternalId'] || !$payload['personId']) {
        	throw new \Exception('Colaborador não encontrado na nexti!');
        }

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	} 
}