
<div id="{{ $id }}" class="{{ $id }}-ui-lockout-wrapper @if($class){{ $class }}@endif">
    @if($shouldDisplay())
        @if ($customView)
            @include($customView)
        @else
            @if ($mode === 'blur')
                @include('ui-lockout::modes.blur')
            @else
                @include('ui-lockout::modes.solid')
            @endif
        @endif
    @else
        {{-- Show main content when not expired --}}
        {{ $slot }}
    @endif
</div>


<style>
    .{{ $id }}-ui-lockout-wrapper {
        padding: 0;
        margin: 0;
    }
    
    .ui-lockout-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: var(--lockout-opacity, 1);
        transition: opacity 0.3s ease;
    }

    .ui-lockout-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .ui-lockout-background-content {
        position: relative;
        z-index: 1;
    }

    .ui-lockout-message {
        margin: 0 0 1rem 0;
        font-size: 1.125rem;
        line-height: 1.75;
        color: #4b5563;
    }
        
    .ui-lockout-title {
        margin: 0 0 1rem 0;
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
    }

    .ui-lockout-icon svg {
        width: 100%;
        height: 100%;
    }

    .ui-lockout-icon {
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1.5rem;
    }
</style>