<?php

namespace Snl\LaravelUiLockout;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use Snl\LaravelUiLockout\Components\UiLockout;
use Snl\LaravelUiLockout\Exceptions\FrameworkNotSupportedException;

class LaravelUiLockoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Ensure this package is only used with Laravel
        $this->ensureLaravelFramework();

        $this->mergeConfigFrom(
            __DIR__ . '/../config/ui-lockout.php',
            'ui-lockout'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/ui-lockout.php' => config_path('ui-lockout.php'),
        ], 'ui-lockout-config');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ui-lockout');

        // Register the component with custom or default tag
        $componentTag = config('ui-lockout.component_tag', 'snl-ui-lockout');
        
        Blade::component($componentTag, UiLockout::class);

        // Log package registration in non-production environments
        if (!app()->environment('production')) {
            logger()->info('Laravel UI Lockout package loaded', [
                'component_tag' => $componentTag,
                'laravel_version' => app()->version(),
            ]);
        }
    }

    /**
     * Ensure the package is running on Laravel framework.
     *
     * @throws FrameworkNotSupportedException
     */
    protected function ensureLaravelFramework(): void
    {
        if (!$this->app instanceof Application) {
            throw FrameworkNotSupportedException::create();
        }

        // Verify minimum Laravel version (10.0)
        if (version_compare(app()->version(), '10.0.0', '<')) {
            throw new FrameworkNotSupportedException(
                'Laravel UI Lockout requires Laravel 10.x or higher. Current version: ' . app()->version()
            );
        }
    }
}
