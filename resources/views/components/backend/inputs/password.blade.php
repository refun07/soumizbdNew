<div class="{{ $labelInline ? 'theme-input-group' : '' }} w-full {{ !$label ? 'no-label' : '' }}">
    @if ($label)
        <label for="{{ $name }}"
            class="theme-input-label {{ $labelInline ? '' : 'pt-0' }} {{ $isRequired ? 'input-required' : '' }}">
            {{ translate($label) }}
        </label>
    @endif

    <div class="theme-input-wrapper flex gap-3">
        <input type="password" id="{{ $name }}" name="{{ $name }}"
            {{ $attributes->merge(['class' => 'theme-input']) }} placeholder="{{ translate($placeholder) }}"
            @required($isRequired) @disabled($isDisabled) />
    </div>
</div>
