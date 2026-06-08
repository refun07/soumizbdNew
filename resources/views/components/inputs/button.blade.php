<button type="{{ $type }}"
    {{ $attributes->merge(['class' => 'whitespace-nowrap h-10 inline-flex items-center gap-2 bg-primary text-white rounded-md px-4 font-bold border-2 border-transparent']) }}>
    {{ translate($buttonText) }}
</button>
