<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Component Tag Name
    |--------------------------------------------------------------------------
    |
    | This value determines the tag name for your component.
    | By default, it's 'snl-ui-lockout', so you can use <x-snl-ui-lockout>.
    | You can change this to any name you prefer, e.g., 'view' to use <x-view>.
    |
    */

    'component_tag' => 'snl-ui-lockout',

    /*
    |--------------------------------------------------------------------------
    | Gibberish Mode
    |--------------------------------------------------------------------------
    | When enabled, the component will convert the background content into gibberish
    | to further obscure it. This is useful for testing or when you want to ensure
    | that no sensitive information is visible even in blurred mode.
    |
    */
    'is_gibberish' => false,

    /*
    |--------------------------------------------------------------------------
    | Expiration Type
    |--------------------------------------------------------------------------
    |
    | The default expiration behavior for the component.
    |
    | Available types:
    | - 'normal': Show component immediately when expired (default)
    | - 'progressive': Gradually increase opacity each day until expiration
    |
    */

    'default_expiration_type' => 'normal',

    /*
    |--------------------------------------------------------------------------
    | Display Mode
    |--------------------------------------------------------------------------
    |
    | The visual style of the lockout screen.
    |
    | Available modes:
    | - 'blur': Blurred backdrop with centered content (default)
    | - 'solid': Solid color background with centered content
    |
    */

    'default_mode' => 'blur',

    /*
    |--------------------------------------------------------------------------
    | Message Type
    |--------------------------------------------------------------------------
    |
    | The type of message to display. This affects the icon and color scheme.
    |
    | Available types:
    | - 'info': Informational message (default)
    | - 'warning': Warning message with warning icon
    | - 'error': Error message with lock icon
    |
    */

    'default_type' => 'info',

    /*
    |--------------------------------------------------------------------------
    | Default Title
    |--------------------------------------------------------------------------
    |
    | The title to display on the lockout screen.
    | Set to null to hide the title.
    |
    */

    'title' => 'Access Restricted',

    /*
    |--------------------------------------------------------------------------
    | Lockout Message
    |--------------------------------------------------------------------------
    |
    | The message to display on the lockout screen when not using API.
    | This is the primary message shown to users when locked out.
    |
    */

    'message' => 'This content is temporarily unavailable.',

    /*
    |--------------------------------------------------------------------------
    | Progressive Days
    |--------------------------------------------------------------------------
    |
    | Number of days for progressive mode to reach full opacity.
    | Each day increases the component opacity proportionally.
    |
    */

    'progressive_days' => 7,

    /*
    |--------------------------------------------------------------------------
    | Expiration Date
    |--------------------------------------------------------------------------
    |
    | The default expiration date for the lockout component.
    | Format: 'YYYY-MM-DD' or null to disable.
    |
    */

    'expires_at' => null,

    /*
    |--------------------------------------------------------------------------
    | Lockout Message API
    |--------------------------------------------------------------------------
    |
    | The URL to fetch the lockout message from.
    | The API should return JSON with a 'message' field.
    | Set to null to disable API fetching.
    |
    */

    'api_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Enable API
    |--------------------------------------------------------------------------
    |
    | Enable or disable API message fetching. When disabled, fallback message
    | will be used. Useful for testing or when API is temporarily unavailable.
    |
    */

    'enable_api' => false,

    /*
    |--------------------------------------------------------------------------
    | API Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait for API response.
    | Default: 5 seconds
    |
    */

    'api_timeout' => 5,

    /*
    |--------------------------------------------------------------------------
    | API Retries
    |--------------------------------------------------------------------------
    |
    | Number of retry attempts if API request fails.
    | Default: 2 retries
    |
    */

    'api_retries' => 2,

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | How long to cache the API response in seconds.
    | Default: 600 seconds (10 minutes)
    |
    */

    'cache_duration' => 600,

    /*
    |--------------------------------------------------------------------------
    | Custom View
    |--------------------------------------------------------------------------
    |
    | Path to a custom Blade view to replace the default lockout body.
    | The custom view will receive all component properties.
    | Example: 'lockout.custom-body' or 'path.to.view'
    |
    */

    'custom_view' => null,

];
