<?php

namespace SmallPHP\Controller;

require_once __DIR__ . "/controller.php";
require_once __DIR__ . "/../services/crypto_service.php";

use SmallPHP\CurrentJwt\TokenData;
use SmallPHP\Controller\HttpResponse;
use SmallPHP\Models\CipherType;
use SmallPHP\Models\CryptoType;
use SmallPHP\Services\CipherService;
use SmallPHP\Services\CryptoService;
use SmallPHP\Services\RansomService;

class CryptoController extends Controller
{
    private CryptoService $service;

    public function __construct(array $endpoint, ?TokenData $token_data, string $method, CryptoService $crypto_service)
    {
        parent::__construct($endpoint, $token_data, $method);

        $this->service = $crypto_service;

        $this->add_endpoint("GET", "list", [$this, "get_all_cryptos"]);
    }

    public function get_all_cryptos(array $query_params, HttpResponse $response): void
    {
        $response->headers["Content-Type"] = "application/json";
        $response->status_code = 200;
        $response->body = json_encode($this->service->get_all_cryptos());
    }

    public function get_name(): string
    {
        return "cryptos";
    }
}
