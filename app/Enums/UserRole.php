<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case MEMBER = 'member';
    case GUEST = 'guest';

    public static function values(): array
    {
        return array_map(fn (UserRole $role) => $role->value, self::cases());
    }
}
