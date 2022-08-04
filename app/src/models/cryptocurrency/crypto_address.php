<?php

namespace SmallPHP\Models;

require_once __DIR__ . "/crypto_type.php";

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
use Doctrine\ORM\Mapping\DiscriminatorMap;

/**
 * @Entity
 * @MappedSuperclass
 * @Table(name="CryptoAddresses")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({"bitcoin_address" = "BitcoinAddress"})
 */
abstract class CryptoAddress
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue
     */
    private int $id;

    /**
     * @var string
     * @Column(name="public_key", type="string")
     */
    protected string $public_key;

    /**
     * @var string
     * @Column(name="address", type="string")
     */
    protected string $address;

    /**
     * @var string
     * @Column(name="private_key", type="string")
     */
    protected string $private_key;

    /**
     * @var Ransom $ransom
     * @OneToOne(targetEntity="Ransom")
     * @JoinColumn(name="ransom_id", referencedColumnName="id")
     */
    private Ransom $ransom;

    public abstract function get_type(): CryptoType;

    public function get_public_key(): string
    {
        return $this->public_key;
    }

    public function get_address(): string
    {
        return $this->address;
    }

    public function get_private_key(): string
    {
        return $this->private_key;
    }

    public function set_encrypted_computer(Ransom $encrypted_computer)
    {
        $this->encrypted_computer = $encrypted_computer;
    }
}
