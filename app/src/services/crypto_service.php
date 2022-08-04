<?php

namespace SmallPHP\Services;

require_once __DIR__ . "/../models/cryptocurrency/bitcoin_address.php";

use SmallPHP\Models\BitcoinAddress;
use SmallPHP\Models\CryptoAddress;
use SmallPHP\Models\CryptoType;

class CryptoService
{
    public function create_crypto_address(CryptoType $crypto_type): CryptoAddress
    {
        return match ($crypto_type)
        {
            CryptoType::CRYPTO_TYPE_BTC => new BitcoinAddress(),
            default => throw new \Exception("Unsupported crypto type"),
        };
    }

    public function get_all_cryptos(): array
    {
        return array_map(function ($cipher_id) { return ["id" => $cipher_id, "name" => $cipher_id->to_string()]; }, CryptoType::cases());
    }
}
