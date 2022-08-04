<?php

namespace SmallPHP\Models;

require_once __DIR__ . "/cipher_type.php";

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
 * @Table(name="Ciphers")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({"xor_cipher" = "XORCipher"})
 */
abstract class Cipher
{
    /**
     * @var int $id
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue
     */
    protected int $id;

    /**
     * @var string $encryption_key
     * @Column(name="encryption_key", type="string")
     */
    protected string $encryption_key;
    /**
     * @var string $decryption_key
     * @Column(name="decryption_key", type="string")
     */
    protected string $decryption_key;

    /**
     * @var Ransom $ransom
     * @OneToOne(targetEntity="Ransom")
     * @JoinColumn(name="ransom_id", referencedColumnName="id")
     */
    private Ransom $ransom;

    public function __construct(string $encryption_key, string $decryption_key)
    {
        $this->encryption_key = $encryption_key;
        $this->decryption_key = $decryption_key;
    }

    public abstract function get_type(): CipherType;

    public function get_encryption_key(): string
    {
        return $this->encryption_key;
    }

    public function get_decryption_key(): string
    {
        return $this->decryption_key;
    }

    public function set_encrypted_computer(Ransom $encrypted_computer)
    {
        $this->encrypted_computer = $encrypted_computer;
    }
}
