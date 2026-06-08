<div class="{{ $labelInline ? 'theme-input-group' : '' }}">
    @if ($label)
        <label for="{{ $name }}"
            class="theme-input-label {{ $labelInline ? '' : 'pt-0' }} {{ $isRequired ? 'input-required' : '' }}">
            {!! translate($label) !!}
        </label>
    @endif

    <div class="theme-input-wrapper {{ $label ? '' : 'col-span-4' }}">
        <input type="number" min="0" id="{{ $name }}" name="{{ $name }}"
            {{ $attributes->merge(['class' => 'theme-input']) }} placeholder="{{ translate($placeholder) }}"
            value="{!! $value !!}" @required($isRequired) @disabled($isDisabled) />
    </div>
</div>
