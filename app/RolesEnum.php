<?php

namespace App;

enum RolesEnum: String
{
    case ADMIN = 'admin';
    case OPERATOR = 'operator';

    public function label(): string
    {
        return match ($this) {
            static::ADMIN => 'Admins',
            static::OPERATOR => 'Operators',
        };
    }

}
