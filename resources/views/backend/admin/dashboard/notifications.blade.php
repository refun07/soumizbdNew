@extends('layouts.admin')

@section('title')
    {{ translate('Notifications') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-bell"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Notifications') }}</span>
        </div>
    </div>

    <ul class="divide-y divide-border">
        @if (count($notifications) > 0)
            @foreach ($notifications as $notification)
                <li class="card mb-2  {{ $notification->is_read ? '' : 'bg-orange-50/50' }}">
                    <a href="{{ getNotificationLink($notification) }}"
                        class="text-[13px] flex items-center px-[25px] py-[15px] gap-[14px]">
                        <div>
                            <span
                                class="w-[27px] aspect-square {{ getNotificationIcon($notification, 'class') }} inline-flex items-center justify-center rounded-full">
                                <i class="{{ getNotificationIcon($notification) }}"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="truncate">
                                {{ getNotificationText($notification) }}
                            </h5>
                            <div class="flex items-center text-muted">
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                <span class="ml-[9px] mr-[7px] h-[5px] w-[5px] rounded-full bg-secondary"></span>
                                <span class="capitalize">{{ $notification->type }}</span>
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        @else
            <div class="text-center py-5">
                {{ translate('No Notifications') }}
            </div>
        @endif
    </ul>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
@endsection
