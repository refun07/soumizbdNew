{{-- $variant: 'primary', 'secondary', 'success', 'danger', 'warning', 'light', 'dark' --}}
<button type="{{ $type }}" {{ $attributes->merge(['class' => 'button button--' . $variant]) }}>
    {{ translate($buttonText) }}
    {{ $slot }}
</button>
