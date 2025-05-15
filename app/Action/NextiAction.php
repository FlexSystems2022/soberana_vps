<?php

namespace App\Action;

use App\Shared\Exceptions\RequestException;

abstract class NextiAction
{
    /**
     * Throw Error Request
     * 
     * @param array $response
     * @param array $request
     * @return void
     * @throws \App\Shared\Exceptions\RequestException
     */
    protected function throwError(array $response, array $request=[]): void
    {
        if($response['code'] >= 200 && $response['code'] < 300) {
            return;
        }

        $json = $response['data'] ?? [];
       
        $message = [
            $json['message'] ?? null
        ];

        if (isset($json['comments']) && sizeof($json['comments']) > 0) {
            $comments = $json['comments'][0] ?? null;

            if ($comments) {
                $message[] = $comments;
            }
        }
        
        $message = array_filter($message);
        if(!$message) {
            throw RequestException::make(
                message: ($response['code'] ?? 400) . ' - Erro ao realizar a requisição',
                code: $response['code'] ?? 400,
                request: $request
            );
        }

        throw RequestException::make(
            message: implode('; ', $message),
            code: $response['code'] ?? 400,
            request: $request
        );
    }
}