<?php

namespace SmallPHP\Models;

enum PlatformType: int
{
    case PLATFORM_TYPE_AMD64 = 1;

    public static function from_string(string $label): ?PlatformType
    {
        return match (strtoupper($label))
        {
            "AMD64" => PlatformType::PLATFORM_TYPE_AMD64,
            default => null,
        };
    }

    public function to_string(): string
    {
        return match ($this)
        {
            PlatformType::PLATFORM_TYPE_AMD64 => "AMD64",
            default => "",
        };
    }
}

