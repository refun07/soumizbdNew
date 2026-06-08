<form
    action="{{ $supplier ? route(routePrefix() . '.suppliers.update', $supplier->id) : route(routePrefix() . '.suppliers.store') }}"
    method="POST">
    @csrf

    @if ($supplier)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type supplier name"
            value="{{ $supplier?->name }}" />

        <x-backend.inputs.text label="Email" name="email" placeholder="Type supplier email"
            value="{{ $supplier?->email }}" />

        <x-backend.inputs.text label="Phone No." name="phone_no" placeholder="Type supplier phone no"
            value="{{ $supplier?->phone_no }}" />

        <x-backend.inputs.textarea label="Address" name="address" placeholder="Type supplier address"
            value="{{ $supplier?->address }}" :isRequired="false" />

        <x-backend.inputs.textarea label="Payment Details" name="payment_details" placeholder="Type payment details"
            value="{{ $supplier?->payment_details }}" :isRequired="false" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Supplier" type="submit" />
        </div>
    </div>
</form>
