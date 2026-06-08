<form class="space-y-3" action="{{ route('admin.general-settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="types[]" value="primaryColor">
    <x-backend.inputs.color label="Primary Color" name="primaryColor" value="{{ getSetting('primaryColor') }}" />

    <input type="hidden" name="types[]" value="secondaryColor">
    <x-backend.inputs.color label="Secondary Color" name="secondaryColor" value="{{ getSetting('secondaryColor') }}" />

    <input type="hidden" name="types[]" value="websiteHeaderLogo">
    <x-backend.inputs.file label="Header Logo" name="websiteHeaderLogo" placeholder="Website header logo"
        value="{{ getSetting('websiteHeaderLogo') }}" />
    <p class="text-xs text-gray-500 mt-1 mb-3">Recommended: 146x37 pixels</p>

    <input type="hidden" name="types[]" value="sellerDashboardLogo">
    <x-backend.inputs.file label="Seller Dashboard Logo" name="sellerDashboardLogo"
        value="{{ getSetting('sellerDashboardLogo') }}" />
    <p class="text-xs text-gray-500 mt-1 mb-3">Recommended: 32x32 pixels</p>

    <input type="hidden" name="types[]" value="websiteFooterLogo">
    <x-backend.inputs.file label="Footer Logo" name="websiteFooterLogo" placeholder="Website footer logo"
        value="{{ getSetting('websiteFooterLogo') }}" />
    <p class="text-xs text-gray-500 mt-1 mb-3">Recommended: 162x36 pixels</p>

    <input type="hidden" name="types[]" value="favicon">
    <x-backend.inputs.file label="Favicon" name="favicon" placeholder="System favicon"
        value="{{ getSetting('favicon') }}" />
    <p class="text-xs text-gray-500 mt-1 mb-3">Recommended: 32x32 pixels</p>

    <input type="hidden" name="types[]" value="poweredBy">
    <x-backend.inputs.file label="Powered by" name="poweredBy" placeholder="Dashboard powered by"
        value="{{ getSetting('poweredBy') }}" />
    <p class="text-xs text-gray-500 mt-1 mb-3">Recommended: 184x47 pixels</p>

    <div class="flex justify-end pt-2">
        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
    </div>
</form>
