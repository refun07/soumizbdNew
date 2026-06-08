<div
    class="input-select2 {{ $labelInline ? 'theme-input-group' : '' }} {{ !$label ? 'no-label' : '' }} {{ $groupClass }}">
    @if ($label)
        <label for="{{ $name }}"
            class="theme-input-label {{ $labelInline ? '' : 'pt-0' }} {{ $isRequired ? 'input-required' : '' }}">{{ translate($label) }}</label>
    @endif

    <div class="theme-input-wrapper flex gap-3 {{ $wrapperClass }}">
        <select {{ $attributes->merge(['class' => $type . ' theme-input']) }} name="{{ $name }}"
            @required($isRequired) @if ($isDisabled) disabled @endif>
            {{ $slot }}
        </select>
        @if ($trashBtn)
            <x-backend.inputs.button type="button" variant="light"
                class="w-10 h-10 !p-0 flex items-center justify-center ms-1" data-toggle="remove-parent"
                data-parent="{{ $trashParent }}">
                <i class="text-red-500 fa-solid fa-trash-can"></i>
            </x-backend.inputs.button>
        @endif
    </div>
</div>
