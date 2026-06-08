<form action="{{ $language ? route('admin.languages.update', $language->id) : route('admin.languages.store') }}"
    method="POST" enctype="multipart/form-data" class="space-y-3">
    @csrf

    @if ($language)
        @method('PUT')
    @endif


    <x-backend.inputs.text label="Add Language" name="name" placeholder="{{ translate('e.g. English') }}"
        value="{{ $language?->name }}" />

    <x-backend.inputs.text label="ISO 639-1 Code" name="code" placeholder="{{ translate('ISO 639-1 Code') }}"
        value="{{ $language?->code }}" isDisabled="{{ isset($language) && $language->id == 1 }}" />

    <x-backend.inputs.select label="Flag" name="flag" class="flag-select2">
        @foreach (\File::files(base_path('public/images/flags')) as $path)
            <x-backend.inputs.select-option name="{{ strtoupper(pathinfo($path)['filename']) }}"
                value="{{ pathinfo($path)['filename'] }}" selected="{{ $language?->flag }}"
                data-flag="{{ asset('images/flags/' . pathinfo($path)['filename'] . '.png') }}" />
        @endforeach
    </x-backend.inputs.select>

    <div class="flex justify-end pt-2">
        <x-backend.inputs.button type="submit"
            buttonText="{{ $language ? translate('Update Language') : translate('Add Language') }}" />
    </div>
</form>
