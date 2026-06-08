<x-backend.inputs.select type="badge-select" :isRequired="false" data-placeholder="{{ translate('Select product badges') }}"
    multiple data-selection-css-class="multi-select2" :isRequired="false" name="badge_ids[]">
    @foreach ($badges as $badge)
        <x-backend.inputs.select-option name="{{ $badge->collectTranslation('name') }}" value="{{ $badge->id }}"
            selected="{{ $productBadges->contains($badge->id) ? $badge->id : '' }}" data-bg="{{ $badge->bg_color }}" />
    @endforeach
</x-backend.inputs.select>
