<?php

namespace Snl\LaravelUiLockout\Exceptions;

/**
 * Exception thrown when the package is used outside Laravel framework.
 */
class FrameworkNotSupportedException extends UiLockoutException
{
    /**
     * Create a new framework not supported exception.
     */
    public static function create(): self
    {
        return new self(
            'Laravel UI Lockout package requires Laravel framework. ' .
            'This package is not compatible with CodeIgniter, Symfony, or other PHP frameworks. ' .
            'Please use this package only in Laravel applications (version 10.x, 11.x, or 12.x).'
        );
    }
}
