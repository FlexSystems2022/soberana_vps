<?php

namespace App\Shared\Enums;

enum TypeEnum: int
{
    case Create = 0;
    case Update = 1;
    case Delete = 3;

    public function label(): string
    {
        return match($this) {
            static::Create => 'Inserir',
            static::Update => 'Atualizar',
            static::Delete => 'Excluir',
            default => $this->value
        };
    }
}