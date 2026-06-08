<div class="theme-input-group {{ !$label ? 'no-label' : '' }}">
    @if ($label)
        <label for="" class="text-foreground">
            {{ translate($label) }}
        </label>
    @endif

    <div class="flex">
        <label
            class="{{ $toggler ? 'theme-checkbox--toggler' : 'theme-checkbox-check' }} options {{ 'theme-checkbox--' . $variant }}">
            <input {{ $attributes->merge(['class' => 'theme-checkbox__input']) }} type="checkbox"
                name="{{ $name }}" value="{!! $value !!}" @checked($isChecked)
                @disabled($isDisabled) />
            <span class="theme-checkbox__checkmark"></span>
        </label>
    </div>
</div>
