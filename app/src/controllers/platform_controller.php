<?php

namespace SmallPHP\Controller;

require_once __DIR__ . "/controller.php";
require_once __DIR__ . "/../services/cipher_service.php";

use SmallPHP\CurrentJwt\TokenData;
use SmallPHP\Services\CipherService;

class CipherController extends Controller
{
    private CipherService $service;

    public function __construct(array $endpoint, ?TokenData $token_data, string $method, CipherService $cipher_service)
    {
        parent::__construct($endpoint, $token_data, $method);

        $this->service = $cipher_service;

        $this->add_endpoint("GET", "list", [$this, "get_all_ciphers"]);
    }

    public function get_all_ciphers(array $query_params, HttpResponse $response): void
    {
        $response->headers["Content-Type"] = "application/json";
        $response->status_code = 200;
        $response->body = json_encode($this->service->get_all_ciphers());
    }

    public function get_name(): string
    {
        return "ciphers";
    }
}
