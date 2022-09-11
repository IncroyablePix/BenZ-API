<?php

namespace SmallPHP\Services;

require_once __DIR__ . "/../models/executable/architecture_type.php";
require_once __DIR__ . "/../models/executable/system_type.php";

use SmallPHP\Models\ArchitectureType;
use SmallPHP\Models\SystemType;

class PlatformService
{
    public function get_all_systems(): array
    {
        return array_map(function ($system_id) { return ["id" => $system_id, "name" => $system_id->to_string()]; }, SystemType::cases());
    }
    public function get_all_architectures(): array
    {
        return array_map(function ($arch_id) { return ["id" => $arch_id, "name" => $arch_id->to_string()]; }, ArchitectureType::cases());
    }
}
