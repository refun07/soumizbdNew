<form action="{{ $tag ? route('admin.tags.update', $tag->id) : route('admin.tags.store') }}" method="POST">
    @csrf

    @if ($tag)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type tag name" value="{{ $tag?->name }}" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Tag" type="submit" />
        </div>
    </div>
</form>
