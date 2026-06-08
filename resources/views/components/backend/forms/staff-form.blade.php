<form action="{{ $staff ? route('admin.staffs.update', $staff->id) : route('admin.staffs.store') }}" method="POST">
    @csrf

    @if ($staff)
        @method('PUT')
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type staff name" value="{{ $staff?->name }}" />

        <x-backend.inputs.text label="Email" name="email" placeholder="Type staff email" value="{{ $staff?->email }}" />

        <x-backend.inputs.text label="Phone No." name="phone_no" placeholder="Type staff phone no"
            value="{{ $staff?->phone }}" />

        <x-backend.inputs.select label="Role" name="role_id" class="">
            @foreach ($roles as $role)
                <x-backend.inputs.select-option name="{{ $role->name }}" value="{{ $role->id }}"
                    selected="{{ $staff?->role_id == $role->id ? $role->id : 0 }}" />
            @endforeach
        </x-backend.inputs.select>

        <x-backend.inputs.select label="Agent Commission Mode" name="agent_commission_mode" :isRequired="false">
            <x-backend.inputs.select-option name="Percentage Only" value="percentage"
                selected="{{ ($staff?->agent_commission_mode ?? 'percentage') === 'percentage' ? 'percentage' : 0 }}" />
            <x-backend.inputs.select-option name="Fixed Amount Only" value="fixed"
                selected="{{ ($staff?->agent_commission_mode ?? 'percentage') === 'fixed' ? 'fixed' : 0 }}" />
            <x-backend.inputs.select-option name="Both — Use Highest" value="both_highest"
                selected="{{ ($staff?->agent_commission_mode ?? 'percentage') === 'both_highest' ? 'both_highest' : 0 }}" />
            <x-backend.inputs.select-option name="Both — Additive (Sum)" value="both_additive"
                selected="{{ ($staff?->agent_commission_mode ?? 'percentage') === 'both_additive' ? 'both_additive' : 0 }}" />
        </x-backend.inputs.select>

        <div id="commission-percentage-field">
            <x-backend.inputs.number label="Agent Commission (%)" name="agent_commission_percentage" placeholder="0.00"
                value="{{ $staff?->agent_commission_percentage ?? 0 }}" :isRequired="false"
                step="0.01" min="0" max="100" />
        </div>

        <div id="commission-fixed-field">
            <x-backend.inputs.number label="Agent Commission Fixed Amount" name="agent_commission_fixed_amount" placeholder="0.00"
                value="{{ $staff?->agent_commission_fixed_amount ?? 0 }}" :isRequired="false"
                step="0.01" min="0" />
        </div>

        <x-backend.inputs.password label="Password" name="password" placeholder="" value="" :isRequired="false" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save staff" type="submit" />
        </div>
    </div>
</form>

<script>
(function() {
    var modeSelect = document.querySelector('select[name="agent_commission_mode"]');
    var percentageField = document.getElementById('commission-percentage-field');
    var fixedField = document.getElementById('commission-fixed-field');

    if (!modeSelect) return;

    function toggleFields() {
        var mode = modeSelect.value;
        if (percentageField) {
            percentageField.style.display = (mode === 'fixed') ? 'none' : '';
        }
        if (fixedField) {
            fixedField.style.display = (mode === 'percentage') ? 'none' : '';
        }
    }

    modeSelect.addEventListener('change', toggleFields);
    toggleFields();
})();
</script>
