<?php

namespace SmallPHP\Models;

enum CipherType: int
{
    case CIPHER_TYPE_XOR = 1;


    public static function from_string(string $label): ?CipherType
    {
        return match (strtoupper($label))
        {
            "XOR" => CipherType::CIPHER_TYPE_XOR,
            default => null,
        };
    }

    public function to_string(): string
    {
        return match ($this)
        {
            CipherType::CIPHER_TYPE_XOR => "XOR",
            default => "",
        };
    }
}

