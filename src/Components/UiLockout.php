<?php

namespace Snl\LaravelUiLockout\Components;

use Illuminate\View\Component;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Snl\LaravelUiLockout\Exceptions\InvalidConfigurationException;

class UiLockout extends Component
{
    public ?string $expiresAt;
    public string $expirationType;
    public bool $isExpired;
    public float $opacity;
    public string $id;
    public ?string $class;
    public ?string $customView;
    public string $mode;
    public string $message;
    public ?string $title;
    public bool $isGibberish = false;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $expiresAt = null,
        ?string $mode = null,
        ?string $type = null,
        ?string $id = null,
        ?string $class = null,
        ?string $customView = null,
        bool $isGibberish = false
    ) {
        $this->expiresAt = $expiresAt ?: config('ui-lockout.expires_at');
        $this->expirationType = $this->validateExpirationType($type ?: config('ui-lockout.default_expiration_type', 'normal'));
        $this->mode = $this->validateMode($mode ?: config('ui-lockout.default_mode', 'blur'));
        $this->id = $this->sanitizeId($id ?: 'a' . Str::random(3));
        $this->class = $this->sanitizeClass($class);
        $this->customView = $this->validateCustomView($customView ?: config('ui-lockout.custom_view'));
        $this->isGibberish = $isGibberish ?: config('ui-lockout.is_gibberish', false);
        $this->message = config('ui-lockout.message', 'This content is temporarily unavailable.');
        $this->title = config('ui-lockout.title', 'Access Restricted');
        $this->calculateExpiration();
    }

    /**
     * Validate and sanitize the expiration type.
     */
    protected function validateExpirationType(?string $type): string
    {
        $allowedTypes = ['normal', 'progressive'];
        return in_array($type, $allowedTypes, true) ? $type : 'normal';
    }

    /**
     * Validate and sanitize the display mode.
     */
    protected function validateMode(?string $mode): string
    {
        $allowedModes = ['blur', 'solid'];
        return in_array($mode, $allowedModes, true) ? $mode : 'blur';
    }

    /**
     * Validate and sanitize the message type.
     */
    protected function validateType(?string $type): string
    {
        $allowedTypes = ['error', 'warning', 'info'];
        return in_array($type, $allowedTypes, true) ? $type : 'info';
    }

    /**
     * Sanitize the component ID to prevent XSS.
     */
    protected function sanitizeId(string $id): string
    {
        // Only allow alphanumeric characters and hyphens
        return preg_replace('/[^a-zA-Z0-9-]/', '', $id) ?: 'a' . Str::random(3);
    }

    /**
     * Sanitize the class attribute to prevent XSS.
     */
    protected function sanitizeClass(?string $class): ?string
    {
        if (!$class) {
            return null;
        }
        // Only allow alphanumeric, spaces, hyphens, and underscores
        return preg_replace('/[^a-zA-Z0-9\s_-]/', '', $class);
    }

    /**
     * Validate custom view path to prevent path traversal attacks.
     */
    protected function validateCustomView(?string $viewPath): ?string
    {
        if (!$viewPath) {
            return null;
        }

        // Prevent path traversal attacks
        if (str_contains($viewPath, '..') || str_contains($viewPath, '\\')) {
            if (app()->environment('local', 'development')) {
                Log::warning('UI Lockout: Invalid custom view path attempted', [
                    'path' => $viewPath,
                    'reason' => 'Path traversal attempt detected'
                ]);
            }
            return null;
        }

        // Only allow alphanumeric, dots, hyphens, underscores, and forward slashes for Laravel view notation
        // Examples: 'lockout.custom', 'admin.lockout', 'layouts/lockout'
        if (!preg_match('/^[a-zA-Z0-9._\/-]+$/', $viewPath)) {
            if (app()->environment('local', 'development')) {
                Log::warning('UI Lockout: Invalid custom view path attempted', [
                    'path' => $viewPath,
                    'reason' => 'Invalid characters in path'
                ]);
            }
            return null;
        }

        return $viewPath;
    }

    /**
     * Calculate expiration status and opacity.
     */
    protected function calculateExpiration(): void
    {
        if (!$this->expiresAt) {
            
            $this->isExpired = true;
            $this->opacity = 1.0;
            return;
        }

        try {
            $expirationDate = Carbon::parse($this->expiresAt);
        } catch (\Exception $e) {
            Log::error('UI Lockout: Invalid expiration date format', [
                'expires_at' => $this->expiresAt,
                'error' => $e->getMessage()
            ]);
            $this->isExpired = true;
            $this->opacity = 1.0;
            return;
        }

        $now = Carbon::now();

        if ($this->expirationType === 'progressive') {
            $progressiveDays = config('ui-lockout.progressive_days', 7);
            
            if ($now->greaterThanOrEqualTo($expirationDate)) {
                $this->isExpired = true;
                $this->opacity = 1.0;
            } else {
                $daysUntilExpiration = $now->diffInDays($expirationDate);
                
                if ($daysUntilExpiration >= $progressiveDays) {
                    $this->isExpired = false;
                    $this->opacity = 0;
                } else {
                    
                    $this->isExpired = false;
                    $daysPassed = $progressiveDays - $daysUntilExpiration;
                    $this->opacity = min(1.0, ($daysPassed / $progressiveDays));
                }
            }
        } else {
            // Normal mode
            if ($now->greaterThanOrEqualTo($expirationDate)) {
                $this->isExpired = true;
                $this->opacity = 1.0;
            } else {
                $this->isExpired = false;
                $this->opacity = 0;
            }
        }
    }

    /**
     * Determine if the component should be displayed.
     */
    public function shouldDisplay(): bool
    {
        if (!$this->expiresAt) {
            return true;
        }

        if ($this->expirationType === 'normal') {
            return $this->isExpired;
        }

        return $this->opacity > 0;
    }


    /**
     * Fetch lockout message from API and cache it.
     */
    public function getLockoutMessage(): ?string
    {
        $apiUrl = config('ui-lockout.api_url');
        $cacheDuration = config('ui-lockout.cache_duration', 600);
        $enableApi = config('ui-lockout.enable_api', true);

        if (!$enableApi || !$apiUrl) {
            return config('ui-lockout.fallback_message', 'Access to this application is currently restricted.');
        }

        $rateLimitKey = 'ui-lockout-api:' . md5($apiUrl);
        
        return Cache::remember('ui_lockout_message', $cacheDuration, function () use ($apiUrl, $rateLimitKey) {
            
            if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
                Log::warning('UI Lockout: API rate limit exceeded', ['url' => $apiUrl]);
                return config('ui-lockout.fallback_message', 'Access to this application is currently restricted.');
            }

            try {
                RateLimiter::hit($rateLimitKey, 60);
                
                $response = Http::timeout(config('ui-lockout.api_timeout', 5))
                    ->retry(config('ui-lockout.api_retries', 2), 100)
                    ->get($apiUrl);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (is_array($data) && isset($data['message']) && is_string($data['message'])) {
                        return $data['message'];
                    }
                    
                    Log::warning('UI Lockout: Invalid API response structure', [
                        'url' => $apiUrl,
                        'status' => $response->status()
                    ]);
                }
                
                if (!app()->environment('production')) {
                    Log::info('UI Lockout: API request failed', [
                        'url' => $apiUrl,
                        'status' => $response->status()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('UI Lockout: API request exception', [
                    'url' => $apiUrl,
                    'error' => $e->getMessage(),
                    'trace' => app()->environment('local') ? $e->getTraceAsString() : null
                ]);
            }
            
            return config('ui-lockout.fallback_message', 'Access to this application is currently restricted.');
        });
    }

    /**
     * Replace HTML text content with gibberish while maintaining exact length and spaces.
     * 
     * @param string $html The HTML string to obfuscate
     * @return \Illuminate\Support\HtmlString The obfuscated HTML with gibberish text
     */
    public function htmlToGibberish(string $html): HtmlString
    {
        $obfuscated = preg_replace_callback(
            '/>([^<]+)</i',
            function ($matches) {
                $text = $matches[1];
                $gibberish = '';
                
                for ($i = 0; $i < strlen($text); $i++) {
                    $char = $text[$i];
                    
                    if (ctype_space($char)) {
                        $gibberish .= $char;
                    } 
                    
                    elseif (ctype_alpha($char)) {
                        if (ctype_upper($char)) {
                            $gibberish .= chr(rand(65, 90)); 
                        } else {
                            $gibberish .= chr(rand(97, 122)); 
                        }
                    }
                    
                    elseif (ctype_digit($char)) {
                        $gibberish .= rand(0, 9);
                    }
                    
                    else {
                        $random = rand(0, 35);
                        if ($random < 10) {
                            $gibberish .= $random; // 0-9
                        } else {
                            $gibberish .= chr($random - 10 + 97); // a-z
                        }
                    }
                }
                
                return '>' . $gibberish . '<';
            },
            $html
        );
        
        return new HtmlString($obfuscated);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('ui-lockout::component');
    }
}
