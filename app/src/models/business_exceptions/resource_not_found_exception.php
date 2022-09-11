<?php

namespace SmallPHP\Models;

class ResourceNotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message} in {$this->file} on line {$this->line}";
    }

    public function get_message(): string
    {
        return $this->message;
    }

    public function get_code(): int
    {
        return $this->code;
    }
}
