<form action="{{ $tax ? route('admin.taxes.update', $tax->id) : route('admin.taxes.store') }}" method="POST">
    @csrf

    @if ($tax)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type tax name" value="{{ $tax?->name }}" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Tax" type="submit" />
        </div>
    </div>
</form>
