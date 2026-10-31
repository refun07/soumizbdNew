@php($inputId = $attributes->get('id') ?: (str_contains($name, '[') ? 'field-' . \Illuminate\Support\Str::uuid() : $name))
<div class="{{ $labelInline ? 'theme-input-group' : '' }}">
    @if ($label)
        <label for="{{ $inputId }}"
            class="theme-input-label {{ $labelInline ? '' : 'pt-0' }} {{ $isRequired ? 'input-required' : '' }}">
            {!! translate($label) !!}
        </label>
    @endif

    <div class="theme-input-wrapper {{ $label ? '' : 'col-span-4' }}">
        <input type="number" min="{{ $attributes->get('min', 0) }}" id="{{ $inputId }}" name="{{ $name }}"
            {{ $attributes->except(['id', 'min'])->merge(['class' => 'theme-input']) }} placeholder="{{ translate($placeholder) }}"
            value="{!! $value !!}" @required($isRequired) @disabled($isDisabled) />
    </div>
</div>
