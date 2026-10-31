@php($inputId = $attributes->get('id') ?: 'checkbox-' . \Illuminate\Support\Str::uuid())
<div class="theme-input-group {{ !$label ? 'no-label' : '' }}">
    @if ($label)
        <label for="{{ $inputId }}" class="text-foreground">
            {{ translate($label) }}
        </label>
    @endif

    <div class="flex">
        <label
            class="{{ $toggler ? 'theme-checkbox--toggler' : 'theme-checkbox-check' }} options {{ 'theme-checkbox--' . $variant }}">
            <input id="{{ $inputId }}" aria-label="{{ $attributes->get('aria-label') ?: translate($label ?: \Illuminate\Support\Str::headline($name)) }}" {{ $attributes->except(['id','aria-label'])->merge(['class' => 'theme-checkbox__input']) }} type="checkbox"
                name="{{ $name }}" value="{!! $value !!}" @checked($isChecked)
                @disabled($isDisabled) />
            <span class="theme-checkbox__checkmark"></span>
        </label>
    </div>
</div>
