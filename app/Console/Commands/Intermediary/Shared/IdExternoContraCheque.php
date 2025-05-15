<?php

namespace App\Console\Commands\Intermediary\Shared;

trait IdExternoContraCheque
{
    /**
     * Generate Id externo
     * 
     * @param object $object
     * @return string
     **/
    protected function generateIdExterno(object $object): string
    {
        $adjusted = str("{$object->dtpagamento?->format('Ymd')} {$object->numemp} {$object->cdchamada}");
        $adjusted = match(str($object->tpprocesso)->trim()->upper()->toString()) {
            'F' => $adjusted->start('F '),
            '1' => $adjusted->start('13-1 '),
            default => $adjusted
        };

        return $adjusted->slug('-')->toString();
    }
}