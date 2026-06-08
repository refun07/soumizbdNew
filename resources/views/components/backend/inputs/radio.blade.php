<div class="theme-input-group {{ !$label ? 'no-label' : '' }}">
    @if ($label)
        <label for="{{ $name }}" class="theme-input-label">
            {{ translate($label) }}
        </label>
    @endif

    <div class="gap-3">
        {{ $slot }}
    </div>
</div>
