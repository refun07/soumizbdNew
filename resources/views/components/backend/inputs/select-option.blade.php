<option {{ $attributes->merge() }} value="{{ $value }}" @selected($selected != null && $selected == $value)>
    {!! $name !!}
</option>
