{{-- $variant: 'primary', 'secondary', 'success', 'danger', 'warning', 'light', 'dark' --}}
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'button button--' . $variant]) }}>
    {{ $slot }}
</a>
