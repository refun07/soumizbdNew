@extends('layouts.admin')

@section('title')
    {{ translate('Language Settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Languages') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Languages') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-12 gap-3">
        <div class="md:col-span-7 md:row-span-2">
            <div class="card theme-table">
                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th data-breakpoints="xs sm">#</th>
                            <th>{{ translate('Name') }}</th>
                            <th data-breakpoints="xs sm">{{ translate('Flag') }}</th>
                            <th data-breakpoints="xs sm">{{ translate('ISO 639-1 Code') }}</th>
                            <th class="text-right">{{ translate('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($languages as $key => $language)
                            <tr>
                                <td>{{ $key + 1 + ($languages->currentPage() - 1) * $languages->perPage() }}</td>
                                <td>{{ $language->name }}</td>
                                <td>
                                    <img src="{{ asset('images/flags/' . $language->flag . '.png') }}"
                                        alt="{{ $language->flag }}">
                                </td>
                                <td>{{ $language->code }}</td>

                                <td class="text-end">
                                    <div class="option-dropdown" tabindex="0">
                                        <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                            <span>{{ translate('Actions') }}</span>
                                        </div>

                                        <div class="option-dropdown__options">
                                            <ul>

                                                @can('edit_languages')
                                                    <li>
                                                        <a href="{{ route('admin.languages.show', $language->code) }}"
                                                            title="{{ translate('Translation') }}"
                                                            class="option-dropdown__option">
                                                            {{ translate('Translate') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.languages.edit', $language->id) }}"
                                                            title="{{ translate('Edit') }}" class="option-dropdown__option">
                                                            {{ translate('Edit') }}
                                                        </a>
                                                    </li>
                                                @endcan
                                                @if ($language->id != 1)
                                                    @can('delete_languages')
                                                        <li>
                                                            <x-backend.inputs.delete-link
                                                                href="{{ route('admin.languages.destroy', $language->id) }}" />
                                                        </li>
                                                    @endcan
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card__footer">
                    {{ $languages->links() }}
                </div>
            </div>
        </div>

        @can('edit_languages')
            <div class="md:col-span-5">
                <div class="card">
                    <h4 class="card__title">{{ translate('Default Language') }}</h4>

                    <div class="card__content space-y-3">
                        <form action="{{ route('admin.env-key.update') }}" method="POST"
                            class="theme-input-wrapper space-y-4">
                            @csrf

                            <input type="hidden" name="types[]" value="DEFAULT_LANGUAGE">

                            <x-backend.inputs.select class="flag-select2" label="Default Language" name="DEFAULT_LANGUAGE">
                                @foreach ($languages as $language)
                                    <x-backend.inputs.select-option
                                        data-flag="{{ asset('images/flags/' . $language->flag . '.png') }}"
                                        name="{{ $language->name }}" value="{{ $language->code }}"
                                        selected="{{ config('app.default_language') }}" />
                                @endforeach
                            </x-backend.inputs.select>
                            <div class="flex justify-end pt-2">
                                <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        <div class="md:col-span-5">
            @can('create_languages')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add Language') }}</h4>

                    <div class="card__content">
                        <x-backend.forms.language-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        "use strict";

        function updateRtlStatus(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post("{{ route('admin.languages.updateRtlStatus') }}", {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    notifyMe('success', "{{ translate('RTL status updated successfully') }}");
                    location.reload();
                } else {
                    notifyMe('danger', "{{ translate('Something went wrong') }}");
                }
            });
        }
    </script>
@endsection
