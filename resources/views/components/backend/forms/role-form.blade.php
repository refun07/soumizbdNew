<form action="{{ $role ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}" method="POST">
    @csrf

    @if ($role)
        @method('PUT')
    @endif

    <div class="grid md:grid-cols-6 gap-3">
        <div class="md:col-span-4 card">

            <div class="overflow-x-auto">
                <table class="theme-table w-full [&_td]:text-foreground min-w-[450px]">
                    <thead class="theme-table__head">
                        <tr>
                            <th>{{ translate('Sections') }}</th>
                            <th class="text-center">{{ translate('Full Access') }}</th>
                            <th class="text-center">{{ translate('View') }}</th>
                            <th class="text-center">{{ translate('Create') }}</th>
                            <th class="text-center">{{ translate('Edit') }}</th>
                            <th class="text-center">{{ translate('Delete') }}</th>
                        </tr>
                    </thead>

                    <tbody class="theme-table__body text-muted">
                        @php
                            $notFourPermissionsInGroup = collect();
                        @endphp
                        @foreach ($permissionGroups as $permissionGroup)
                            @if ($permissionGroup[0]['format'] == 'row')
                                <tr class="permission-group-tr">
                                    <td class="font-bold">
                                        {{ translate(ucwords(str_replace('_', ' ', $permissionGroup[0]['group_name']))) }}
                                    </td>

                                    <td>
                                        <div class="flex justify-center">
                                            <x-backend.inputs.checkbox name="full_access"
                                                onchange="toggleGroupAll(this)" />
                                        </div>
                                    </td>

                                    @foreach ($permissionGroup as $permission)
                                        @php
                                            $checked = false;
                                            if ($role) {
                                                $checked = $role->hasPermissionTo($permission->id);
                                            }
                                        @endphp
                                        <td class="permissions-selection">
                                            <div class="flex justify-center">
                                                <x-backend.inputs.checkbox name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    onchange="initPermissionFullAccessCheck()" :isChecked="$checked" />
                                            </div>
                                        </td>
                                    @endforeach

                                </tr>
                            @else
                                @php
                                    $notFourPermissionsInGroup->push($permissionGroup);
                                @endphp
                            @endif
                        @endforeach
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                        @foreach ($notFourPermissionsInGroup as $permissionGroup)
                            <tr class="permission-group-tr">
                                <td class="flex flex-col gap-3">
                                    <div class="font-bold flex items-center gap-3 mb-1">
                                        <x-backend.inputs.checkbox name="full_access" onchange="toggleGroupAll(this)" />
                                        <span>
                                            {{ translate(ucwords(str_replace('_', ' ', $permissionGroup[0]['group_name']))) }}
                                        </span>
                                    </div>

                                    @foreach ($permissionGroup as $permission)
                                        @php
                                            $checked = false;
                                            if ($role) {
                                                $checked = $role->hasPermissionTo($permission->id);
                                            }
                                        @endphp
                                        <div class="!text-muted flex items-center gap-3 ms-8 permissions-selection">
                                            <x-backend.inputs.checkbox name="permissions[]"
                                                value="{{ $permission->id }}" id="permissions-{{ $permission->id }}"
                                                onchange="initPermissionFullAccessCheck()" :isChecked="$checked" />
                                            <label for="permissions-{{ $permission->id }}">
                                                {{ translate(ucwords(str_replace('_', ' ', $permission->name))) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </td>
                                <td colspan="5"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="card sticky top-0">
                <h4 class="card__title">{{ translate('Role Information') }}</h4>
                <div class="card__content">
                    <div class="space-y-8">
                        <x-backend.inputs.text label="Role Name" name="name" placeholder="Type role name..."
                            value="{{ $role?->name ?? '' }}" />

                        @php
                            $isChecked = false;
                            if ($role) {
                                $isChecked = $role->is_active == 1;
                            }
                        @endphp
                        <x-backend.inputs.checkbox label="Active Status" toggler="true" name="is_active" value="1"
                            :isChecked="$isChecked" />

                        <div class="flex justify-end">
                            <x-backend.inputs.button buttonText="Save Changes" type="submit" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
