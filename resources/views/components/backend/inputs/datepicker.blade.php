<div class="{{ $labelInline ? 'theme-input-group' : '' }} {{ !$label ? 'no-label' : '' }}">
    @if ($label)
        <label for="{{ $name }}"
            class="theme-input-label {{ $labelInline ? '' : 'pt-0' }} {{ $isRequired ? 'input-required' : '' }}">
            {{ translate($label) }}
        </label>
    @endif
    <div class="theme-input-wrapper {{ $label ? '' : 'col-span-4' }}">
        @if ($htmlDateInput)
            <input type="datetime-local" placeholder="{!! translate($placeholder) !!}" class="theme-input" id="{{ $name }}"
                name="{{ $name }}" value="{!! $value !!}" @disabled($isDisabled)   />
        @else
            <div class="date-picker date-picker--{{ $type }}">
                <input type="text" placeholder="{!! translate($placeholder) !!}" class="theme-input" id="{{ $name }}"
                    name="{{ $name }}" value="{!! $value !!}" @disabled($isDisabled)   />
            </div>
        @endif
    </div>

</div>


