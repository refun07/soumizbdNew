@extends('layouts.admin')

@section('title')
    {{ translate('Database Backups') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-database"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Database Backups') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Database Backups') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card theme-table">
        <div class="card__title border-none flex flex-col md:flex-row items-center justify-between gap-3 mb-4">
            <h4 class="text-lg font-bold text-secondary">{{ translate('All Backups') }}</h4>
            
            <form action="{{ route('admin.database-backups.create') }}" method="POST">
                @csrf
                <x-backend.inputs.button type="submit" buttonText="{{ translate('Create Backup') }}" />
            </form>
        </div>

        <table class="product-list-table footable w-full">
            <thead class="uppercase text-left bg-primary/10">
                <tr>
                    <th>#</th>
                    <th>{{ translate('Filename') }}</th>
                    <th>{{ translate('File Size') }}</th>
                    <th>{{ translate('Created Date') }}</th>
                    <th class="w-[130px] text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($backups as $key => $backup)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td class="font-mono text-sm text-secondary">{{ $backup['name'] }}</td>
                        <td>{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                        <td>{{ $backup['created_at']->toDateTimeString() }}</td>
                        <td>
                            <div class="option-dropdown" tabindex="0">
                                <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                    <span>{{ translate('Actions') }}</span>
                                </div>

                                <div class="option-dropdown__options">
                                    <ul>
                                        <li>
                                            <a href="{{ route('admin.database-backups.download', $backup['name']) }}"
                                                class="option-dropdown__option">
                                                {{ translate('Download') }}
                                            </a>
                                        </li>
                                        <li>
                                            <x-backend.inputs.delete-link
                                                href="{{ route('admin.database-backups.delete', $backup['name']) }}" />
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-muted">
                            {{ translate('No backups found. Click "Create Backup" to generate one.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
