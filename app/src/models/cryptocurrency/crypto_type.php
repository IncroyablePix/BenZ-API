<?php

namespace SmallPHP\Models;

enum CryptoType: int
{
    case CRYPTO_TYPE_BTC = 1;

    public static function from_string(string $label): ?CryptoType
    {
        return match (strtoupper($label))
        {
            "BTC" => CryptoType::CRYPTO_TYPE_BTC,
            default => null,
        };
    }

    public function to_string(): string
    {
        return match ($this)
        {
            CryptoType::CRYPTO_TYPE_BTC => "BTC",
            default => "",
        };
    }
}
