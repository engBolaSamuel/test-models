<?php

namespace App\Enums;

enum IndexType: string
{
    case None = 'none';
    case Primary = 'primary';
    case Unique = 'unique';
    case Index = 'index';

    public function label(): string
    {
        return match ($this) {
            self::None => 'None',
            self::Primary => 'Primary',
            self::Unique => 'Unique',
            self::Index => 'Index',
        };
    }
}
