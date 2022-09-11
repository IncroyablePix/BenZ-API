<?php

namespace SmallPHP\Models;

enum ArchitectureType: int
{
    case PLATFORM_TYPE_AMD64 = 1;

    public static function from_string(string $label): ?ArchitectureType
    {
        return match (strtoupper($label))
        {
            "AMD64" => ArchitectureType::PLATFORM_TYPE_AMD64,
            default => null,
        };
    }

    public function to_string(): string
    {
        return match ($this)
        {
            ArchitectureType::PLATFORM_TYPE_AMD64 => "AMD64",
            default => "",
        };
    }
}

