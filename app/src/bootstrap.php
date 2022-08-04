<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/models"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

// database configuration parameters
// doctrine2 pgsql
$conn = array(
    'driver' => 'pdo_pgsql',
    "user" => getenv("POSTGRES_USER"),
    "password" => getenv("POSTGRES_PASSWORD"),
    "dbname" => getenv("POSTGRES_DB"),
    'host' => getenv("POSTGRES_HOST"),
    "port" => (int)getenv("POSTGRES_PORT"),
    'charset' => 'utf8',
);

// obtaining the entity manager
global $entityManager;
$entityManager = EntityManager::create($conn, $config);
