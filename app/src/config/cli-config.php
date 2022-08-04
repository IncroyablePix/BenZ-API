<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . '/../bootstrap.php';

global $entityManager;

return ConsoleRunner::createHelperSet($entityManager); // For use with doctrine2!
