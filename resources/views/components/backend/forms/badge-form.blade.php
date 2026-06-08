<form action="{{ $badge ? route('admin.badges.update', $badge->id) : route('admin.badges.store') }}" method="POST">
    @csrf

    @if ($badge)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">

        <x-backend.inputs.text label="Name" name="name" placeholder="Type badge name"
            value="{{ $badge?->collectTranslation('name') }}" />

        <x-backend.inputs.color label="Text Color" name="color" value="{{ $badge?->color ?? '#ffffff' }}" />
        <x-backend.inputs.color label="Background Color" name="bg_color" value="{{ $badge?->bg_color }}" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Badge" type="submit" />
        </div>
    </div>
</form>
