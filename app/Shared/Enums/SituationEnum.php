<?php

namespace App\Shared\Enums;

enum SituationEnum: int
{
    case Pendent = 0;
    case Success = 1;
    case Error = 2;

    public function label(): string
    {
        return match($this) {
            static::Pendent => 'Pendente',
            static::Success => 'Processado',
            static::Error => 'Erro',
            default => $this->value
        };
    }
}