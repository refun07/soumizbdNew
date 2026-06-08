<div class="{{ $labelInline ? 'theme-input-group' : '' }} w-full {{ !$label ? 'no-label' : '' }}">
    @if ($label)
        <label for="{{ $name }}" class="theme-input-label {{ $isRequired ? 'input-required' : '' }}">
            {{ translate($label) }}
        </label>
    @endif

    <div class="theme-input-wrapper">
        <input type="color" id="{{ $name }}" name="{{ $name }}"
            {{ $attributes->merge(['class' => 'w-full h-10 p-2 rounded']) }} placeholder="{{ translate($placeholder) }}"
            value="{!! $value !!}" @required($isRequired) @disabled($isDisabled) />
    </div>
</div>
