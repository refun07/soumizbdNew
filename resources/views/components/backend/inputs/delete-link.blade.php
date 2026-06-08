<a href="javascript:void(0);" data-href="{{ $href }}" data-title="{{ translate($title) }}"
    data-text="{{ translate($text) }}" data-method="DELETE" data-micromodal-trigger="confirm-modal"
    {{ $attributes->merge(['class' => 'option-dropdown__option confirm-modal']) }}>
    {!! translate($btnText) !!}
</a>
