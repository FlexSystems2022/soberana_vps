<?php

namespace App\Shared\Exceptions;

class RequestException extends \Exception
{
    /**
     * @var array
     */
    protected array $request = [];

    /**
     * Make Exception
     * @param string $message
     * @param int $code
     * @param array $request
     * 
     * @return RequestException
     */
    public static function make(string $message, int $code, array $request=[]): self
    {
        $except = new static($message, $code);
        $except->request = $request;
        return $except;
    }

    /**
     * Get Request
     * 
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request ?? [];
    }
}