<label class="theme-radio options">
    <input type="{{ $type }}" class="theme-radio__input" name="{{ $name }}" value="{{ $value }}"
        @checked($value == $checkedValue) />
    <span class="theme-radio__checkmark"></span>
    <span class="theme-radio__text text-sm">{!! $label !!}</span>
</label>
