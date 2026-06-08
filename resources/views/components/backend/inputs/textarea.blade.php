<div class="{{ $labelInline ? 'theme-input-group' : '' }}  {{ !$label ? 'no-label' : '' }}">
    @if ($label)
        <label for="{{ $name }}"
            class="theme-input-label {{ $labelInline ? '' : 'pt-0' }} {{ $isRequired ? 'input-required' : '' }}">
            {{ translate($label) }}
        </label>
    @endif

    <div class="theme-input-wrapper flex gap-3 {{ $label ? '' : 'col-span-4' }}">
        <textarea class="theme-input theme-textarea {{ $rich ? 'jodit-editor' : '' }}" placeholder="{{ translate($placeholder) }}"
            rows="{{ $rows }}" id="{{ $name }}" name="{{ $name }}" @required(!$rich && $isRequired)
            @disabled($isDisabled)>{!! $value !!}</textarea>
        @if ($aiGenerate)
            <div>
                <x-backend.inputs.button type="button"
                    class="!bg-[#8952FF] w-10 h-10 !p-0 flex items-center justify-center"
                    id="generate-{{ $name }}" onclick="generateOpenAIContent('{{ $name }}')">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </x-backend.inputs.button>
            </div>
        @endif

        @if ($trashBtn)
            <x-backend.inputs.button type="button" variant="light"
                class="w-10 h-10 !p-0 flex items-center justify-center ms-1" data-toggle="remove-parent"
                data-parent="{{ $trashParent }}">
                <i class="text-red-500 fa-solid fa-trash-can"></i>
            </x-backend.inputs.button>
        @endif
    </div>
</div>
