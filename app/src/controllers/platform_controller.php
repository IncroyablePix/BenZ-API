<?php

namespace SmallPHP\Controller;

require_once __DIR__ . "/controller.php";
require_once __DIR__ . "/../services/platform_service.php";

use SmallPHP\CurrentJwt\TokenData;
use SmallPHP\Services\PlatformService;

class PlatformController extends Controller
{
    private PlatformService $service;

    public function __construct(array $endpoint, ?TokenData $token_data, string $method, PlatformService $cipher_service)
    {
        parent::__construct($endpoint, $token_data, $method);

        $this->service = $cipher_service;

        $this->add_endpoint("GET", "architectures", [$this, "get_all_ciphers"]);
        $this->add_endpoint("GET", "systems", [$this, "get_all_systems"]);
    }

    public function get_all_architectures(array $query_params, HttpResponse $response): void
    {
        $response->headers["Content-Type"] = "application/json";
        $response->status_code = 200;
        $response->body = json_encode($this->service->get_all_architectures());
    }

    public function get_all_systems(array $query_params, HttpResponse $response): void
    {
        $response->headers["Content-Type"] = "application/json";
        $response->status_code = 200;
        $response->body = json_encode($this->service->get_all_systems());
    }

    public function get_name(): string
    {
        return "platforms";
    }
}
