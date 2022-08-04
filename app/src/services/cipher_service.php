<?php

namespace SmallPHP\Services;

require_once __DIR__ . "/../models/cipher/cipher_type.php";
require_once __DIR__ . "/../models/cipher/xor_cipher.php";

use SmallPHP\Models\Cipher;
use SmallPHP\Models\CipherType;
use SmallPHP\Models\XORCipher;

class CipherService
{
    public function create_cipher(CipherType $cipher_id): Cipher
    {
        return match ($cipher_id)
        {
            CipherType::CIPHER_TYPE_XOR => new XORCipher(),
            default => throw new \Exception("Unsupported cipher type"),
        };
    }

    public function get_all_ciphers(): array
    {
        return array_map(function ($cipher_id) { return ["id" => $cipher_id, "name" => $cipher_id->to_string()]; }, CipherType::cases());
    }
}
