<?php

namespace SmallPHP\Models;

require_once __DIR__ . "/crypto_type.php";
require_once __DIR__ . "/crypto_address.php";

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 */
class BitcoinAddress extends CryptoAddress
{
    public function __construct()
    {
        // TODO: Implement wallet creation
        $this->public_key = "";
        $this->address = "1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa";
        $this->private_key = "";
    }

    public function get_type(): CryptoType
    {
        return CryptoType::CRYPTO_TYPE_BTC;
    }
}
