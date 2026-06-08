@if ($labelInline)
    <div
        {{ $attributes->except(['trashParent'])->merge(['class' => 'theme-input-group theme-input--file-upload ' . (!$label ? 'no-label' : '')]) }}>
    @else
        <div
            {{ $attributes->except(['trashParent'])->merge(['class' => 'theme-input-group theme-input--file-upload !block ' . (!$label ? 'no-label' : '')]) }}>
@endif
@if ($label)
    <div>
        <label class="theme-input-label">
            {{ translate($label) }}
        </label>
    </div>
@endif

<div class="theme-input-wrapper">
    <div class="flex">
        <div class="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0" tabindex="0"
            data-micromodal-trigger="media-manager-modal">
            <div class="px-6 py-3 bg-primary text-white rounded-md rounded-e-none">{{ translate('BROWSE') }}</div>
            <span class="media-count py-2 flex items-center text-muted">{{ translate('No Selected Item') }}</span>
            <input type="hidden" id="{{ $name }}" name="{{ $name }}" class="hidden"
                {{ $multiple ? 'multiple' : '' }} accept="{{ $accept }}" value="{!! $value !!}" />
        </div>

        @if ($trashBtn)
            <x-backend.inputs.button type="button" variant="light"
                class="w-10 h-10 !p-0 flex items-center justify-center ms-1" data-toggle="remove-parent"
                data-parent="{{ $trashParent }}">
                <i class="text-red-500 fa-solid fa-trash-can"></i>
            </x-backend.inputs.button>
        @endif
    </div>
    <span class="text-xs text-muted leading-none">{!! translate($filesHint) !!}</span>

    <div class="file-preview mt-2 flex gap-3 flex-wrap"></div>
</div>
</div>
