<?php

namespace SmallPHP\Services;

require_once __DIR__ . "/../models/executable/architecture_type.php";
require_once __DIR__ . "/../models/executable/system_type.php";

use SmallPHP\Models\ArchitectureType;
use SmallPHP\Models\SystemType;

class SystemService
{
    public function get_all_systems(): array
    {
        return array_map(function ($system_id) { return ["id" => $system_id, "name" => $system_id->to_string()]; }, SystemType::cases());
    }
}
