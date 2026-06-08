<form action="{{ route(routePrefix() . '.shop-sections.store') }}" method="POST">
    @csrf

    <div class="space-y-3">
        <x-backend.inputs.select label="Type" name="type" data-search="false" class="section-type">
            <x-backend.inputs.select-option name="Products" value="products" />
            <x-backend.inputs.select-option name="Full Width Banner" value="full-width-banner" />
            <x-backend.inputs.select-option name="Boxed Width Banner" value="boxed-width-banner" />
        </x-backend.inputs.select>

        <x-backend.inputs.text name="title" label="Title" placeholder="Type section title" class="section-title" />

        <x-backend.inputs.number name="order" label="Order" value="0" min="0" />

        <div class="flex justify-end pt-2">
            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Section') }}" />
        </div>
    </div>
</form>
