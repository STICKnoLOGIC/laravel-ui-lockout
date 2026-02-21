{{-- Background content that will be covered --}}
@if($slot && !$slot->isEmpty())
    <div class="ui-lockout-background-content">
        {{  $isGibberish && $opacity > 0.7 ? $htmlToGibberish($slot) : $slot }}
    </div>
@endif

<div class="ui-lockout-overlay ui-lockout-solid" style="--lockout-opacity: {{ $opacity }};">
    <div class="ui-lockout-backdrop"></div>
        <div class="ui-lockout-content-wrapper">
        <div class="ui-lockout-content-center">
            <div class="ui-lockout-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
            </div>

            @if($title)
                <h2 class="ui-lockout-title">{{ $title }}</h2>
            @endif

            <p class="ui-lockout-message">{{ $message }}</p>
        </div>
        </div>
</div>

<style>
    .ui-lockout-solid {                     
        background-color: white;
    }
    .ui-lockout-solid-warning {
        background-color: rgba(217, 119, 6, 0.85);
    }
    .ui-lockout-solid .ui-lockout-content-center {
        background: transparent;
        border-radius: 0;
        padding: 3rem 2rem;
        max-width: 600px;
        text-align: center;
    }

</style>
