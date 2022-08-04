<?php
require_once __DIR__ . "/bootstrap.php";
require_once __DIR__ . "/controllers/ransom_controller.php";
require_once __DIR__ . "/controllers/cipher_controller.php";
require_once __DIR__ . "/controllers/crypto_controller.php";
require_once __DIR__ . "/services/ransom_service.php";
require_once __DIR__ . "/services/cipher_service.php";
require_once __DIR__ . "/services/crypto_service.php";
require_once __DIR__ . "/utils/cors.php";
require_once __DIR__ . "/utils/headers.php";
require_once __DIR__ . "/utils/jwt.php";

use SmallPHP\Controller\CipherController;
use SmallPHP\Controller\CryptoController;
use SmallPHP\Controller\RansomController;
use SmallPHP\Services\CipherService;
use SmallPHP\Services\CryptoService;
use SmallPHP\Services\RansomService;
use function SmallPHP\Cors\use_cors;
use function SmallPHP\CurrentJwt\extract_token_data;
use function SmallPHP\CurrentJwt\extract_from_jwt;
use function SmallPHP\Headers\get_request_headers;
use SmallPHP\CurrentJwt\InvalidTokenException;
use SmallPHP\Controller\HttpResponse;


global $entityManager;

/*ini_set('log_errors','On');
ini_set('display_errors','Off');
ini_set('error_reporting', 0);*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use_cors();

//--- Request params

$method = $_SERVER["REQUEST_METHOD"];
$request = array_values(array_filter(explode("/", $_GET["path"] ?? ""), function($part) { return !empty($part); }));
$headers = get_request_headers();

//--- Authorization extraction
$token_val = null;

try
{
    $token = extract_from_jwt(array_key_exists("Authorization", $headers) ? $headers["Authorization"] : "");
    $token_val = extract_token_data($token);
}
catch(InvalidTokenException $e)
{
    $token_val = null;
}

//--- Routing

$controllers = [
    new RansomController($request, $token_val, $method, new RansomService($entityManager)),
    new CipherController($request, $token_val, $method, new CipherService()),
    new CryptoController($request, $token_val, $method, new CryptoService())
];

$controller = null;
$response = new HttpResponse();

if(count($request) > 0)
{
    foreach ($controllers as $c)
    {
        if ($c->get_name() == $request[0])
        {
            $controller = $c;
            break;
        }
    }
}

if($controller != null)
    $response = $controller->execute();

//--- Response
foreach($response->headers as $h => $v)
    header($h . ": " . $v);

http_response_code($response->status_code);

echo $response->body;
