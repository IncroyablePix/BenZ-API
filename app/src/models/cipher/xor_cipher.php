<?php

namespace SmallPHP\Models;

require_once __DIR__ . "/cipher.php";

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;

/**
 * @Entity
 */
class XORCipher extends Cipher
{
    public function __construct()
    {
        $key = $this->random_string(64);
        parent::__construct($key, $key);
    }

    public function get_type(): CipherType
    {
        return CipherType::CIPHER_TYPE_XOR;
    }

    private function random_string($len = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max_len = strlen($characters);
        $random_string = '';

        for ($i = 0; $i < $len; $i++)
        {
            $random_string .= $characters[rand(0, $max_len - 1)];
        }

        return $random_string;
    }
}
