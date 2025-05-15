<?php

namespace App\Action\Shift;

use App\Models\Shift;
use App\Shared\Helper;
use App\Action\NextiAction;
use App\Shared\Provider\RestClient;

class UpdateShift extends NextiAction
{
	/**
	 * Action Update Shift
	 * 
	 * @param \App\Shared\Provider\RestClient $client
	 * @param \App\Models\Shift $horario
	 * @return array
	 **/
	public function update(RestClient $client, Shift $horario): array
	{
		try {
	        $payload = $this->makePayload($horario);

	        $request = $client->put('shifts/' . $horario->ID, options: [
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
	 * @param \App\Models\Shift $horario
	 * @return array
	 **/
	private function makePayload(Shift $horario): array
	{
		$payload = [
	        'active' => $horario->ACTIVE == 1,
			'breakpaid' => null,
	        'externalId' => $horario->IDEXTERNO,
	        'generateFlexibleInconsistenciesAlert' => false,
	        'markings' => [],
			'mobilityAfter' => null,
			'mobilityBefore' => null,
			'name' => $horario->DESHOR,
			'shiftTypeId' => $horario->SHIFTTYPEID
		];

		if($horario->ENTRADA1) {
			$payload['markings'][] = $this->makeMarking($horario->ENTRADA1, 1, 1); // ENTRADA/SAÍDA OBRIGATÓRIA
		}

		if($horario->SAIDA1) {
			$payload['markings'][] = $this->makeMarking($horario->SAIDA1, 2, 4); // REFEIÇÃO OBRIGATÓRIA
		}

		if($horario->ENTRADA2) {
			$payload['markings'][] = $this->makeMarking($horario->ENTRADA2, 3, 4); // REFEIÇÃO OBRIGATÓRIA
		}

		if($horario->SAIDA2) {
			$payload['markings'][] = $this->makeMarking($horario->SAIDA2, 4, 1); // ENTRADA/SAÍDA OBRIGATÓRIA
		}

		$payload['markings'] = array_values(array_filter($payload['markings']));

		return array_filter($payload, fn(mixed $value) => !is_null($value));
	}

	/**
	 * Make Marking
	 * 
	 * @param string|null $text
	 * @param int $sequence
	 * @param int $markingTypeId
	 * @return array|null
	 */
	private function makeMarking(?string $text, int $sequence,int $markingTypeId): ?array
	{
		if(!$text) {
			return null;
		}

		$marking = Helper::hourConvertToMinutes($text);

		return [
			'checkinTolerance' => 120,
			'checkoutTolerance' => 120,
			'marking' => intval($marking),
			'markingTypeId' => $markingTypeId,
			'sequence' => $sequence
		];
	}
}