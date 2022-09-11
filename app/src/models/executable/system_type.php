<?php

namespace SmallPHP\Models;

enum SystemType: int
{
    case SYSTEM_TYPE_WINDOWS_GUI = 1;
    // case SYSTEM_TYPE_WINDOWS_CLI = 2;
    // case SYSTEM_TYPE_LINUX_CLI = 3;
    // case SYSTEM_TYPE_APPLE = 5;

    public static function from_string(string $label): ?SystemType
    {
        return match (strtoupper($label))
        {
            "WINDOWS GUI" => SystemType::SYSTEM_TYPE_WINDOWS_GUI,
            default => null,
        };
    }

    public function to_string(): string
    {
        return match ($this)
        {
            SystemType::SYSTEM_TYPE_WINDOWS_GUI => "WINDOWS GUI",
            default => "",
        };
    }
}

