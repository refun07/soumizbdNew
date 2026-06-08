@extends('layouts.admin')

@section('title')
    {{ translate('Translations') }} | {{ getSetting('systemName') }}
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
            <a href="{{ route('admin.languages.index') }}" class="font-bold ">{{ translate('Languages Settings') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Translations') }}</p>
        </div>
    </div>

    <div class="card max-w-[900px]">
        <div class="card__title">
            <button class="toggle-filters toggler max-w-[250px] lg:hidden w-full mb-4" data-target="#products-filter">
                <span>{{ translate('Search And Filter') }}</span>
                <span class="toggle-filters--icon">
                    <i class="fas fa-chevron-down"></i>
                </span>
            </button>

            <div class="theme-table__filter hidden lg:flex max-xl:flex-col xl:items-center justify-end gap-3"
                id="products-filter">
                <form method="GET" class="flex gap-2 max-w-[380px] w-full">
                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="grow" />
                </form>
            </div>
        </div>

        <form action="{{ route('admin.languages.keyValueStore') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $language->id }}">
            <div class="theme-table">
                <table class="translation-table footable w-full">

                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th>#</th>
                            <th class="w-1/2">{{ translate('Key') }}</th>
                            <th class="w-1/2">{{ translate('Value') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($langKeys as $key => $langKey)
                            <tr>
                                <td>{{ $key + 1 + ($langKeys->currentPage() - 1) * $langKeys->perPage() }}</td>
                                <td> <span class="key">{{ $langKey->t_value }}</span></td>
                                <td>
                                    @php
                                        $translation = \App\Models\Translation::where('lang_key', $language->code)
                                            ->where('t_key', $langKey->t_key)
                                            ->latest()
                                            ->first();
                                        if ($translation != null) {
                                            $value = $translation->t_value;
                                        } else {
                                            $value = $langKey->t_value;
                                        }
                                    @endphp
                                    <x-backend.inputs.text class="value" name="values[{{ $langKey->t_key }}]"
                                        value="{!! $value !!}" />
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

                <div class="card__footer">
                    <div class="flex max-md:flex-col max-md:items-start md:justify-end gap-x-3 gap-y-2 mb-3">
                        <x-backend.inputs.button buttonText="Copy Localizations" type="button"
                            onclick="copyLocalizations()" />

                        <x-backend.inputs.button buttonText="Save Translations" type="submit" />
                    </div>

                    {{ $langKeys->links() }}
                </div>
            </div>
        </form>
    </div>
@endsection

    @section('scripts')
    <script type="text/javascript">
        "use strict";

        // translate in one click
        function copyLocalizations() {
            $('.translation-table > tbody  > tr').each(function(index, tr) {
                $(tr).find('.value').val($(tr).find('.key').text());
            });
        }

        // Auto-submit search on Enter key
        $(document).on('keypress', '.search-input', function(e) {
            if (e.which === 13) {
                $(this).closest('form').submit();
            }
        });
    </script>
@endsection
