<?php

namespace Snl\LaravelUiLockout\Exceptions;

/**
 * Exception thrown when configuration is invalid.
 */
class InvalidConfigurationException extends UiLockoutException
{
    /**
     * Create a new invalid configuration exception.
     */
    public static function missingExpirationDate(): self
    {
        return new self('Expiration date is required but not set in config or component attribute.');
    }

    /**
     * Create exception for invalid expiration type.
     */
    public static function invalidExpirationType(string $type): self
    {
        return new self("Invalid expiration type '{$type}'. Allowed types: normal, progressive.");
    }

    /**
     * Create exception for invalid custom view path.
     */
    public static function invalidCustomView(string $path): self
    {
        return new self("Invalid custom view path '{$path}'. Path may contain malicious characters.");
    }
}
