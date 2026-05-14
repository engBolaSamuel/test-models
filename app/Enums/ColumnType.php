<?php

namespace App\Enums;

enum ColumnType: string
{
    case BigInteger = 'bigInteger';
    case Boolean = 'boolean';
    case Date = 'date';
    case DateTime = 'dateTime';
    case Decimal = 'decimal';
    case Float = 'float';
    case Integer = 'integer';
    case Json = 'json';
    case SmallInteger = 'smallInteger';
    case String = 'string';
    case Text = 'text';
    case Timestamp = 'timestamp';
    case UnsignedBigInteger = 'unsignedBigInteger';

    public function label(): string
    {
        return match ($this) {
            self::BigInteger => 'Big Integer',
            self::Boolean => 'Boolean',
            self::Date => 'Date',
            self::DateTime => 'DateTime',
            self::Decimal => 'Decimal',
            self::Float => 'Float',
            self::Integer => 'Integer',
            self::Json => 'JSON',
            self::SmallInteger => 'Small Integer',
            self::String => 'String',
            self::Text => 'Text',
            self::Timestamp => 'Timestamp',
            self::UnsignedBigInteger => 'Unsigned Big Integer',
        };
    }
}
