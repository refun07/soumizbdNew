@extends('layouts.admin')

@section('title')
    {{ translate('Offer Management') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Offer Management') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Offer Management') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-2 gap-3">
        <div class="md:col-span-1">
            <div class="card"> 
                
                <h4 class="card__title capitalize">{{ translate('Compose your offer') }}</h4>
                <div class="card__content pt-0">
                    <div class="grid gap-3 lg:gap-5"> 
                        <form action="{{ route('admin.storeOfferManagement') }}" method="POST">
                            @csrf 
                            <div class="space-y-5 my-4">  
                                <x-backend.inputs.select data-name="engine" name="engine" class="" :isRequired="true" label="Select Engine" :labelInline="false">
                                    <x-backend.inputs.select-option name="{{ translate('WhatsApp') }}" value="whatsapp" /> 
                                    <x-backend.inputs.select-option name="{{ translate('Email') }}" value="email" /> 
                                    <x-backend.inputs.select-option name="{{ translate('SMS') }}" value="sms" /> 
                                </x-backend.inputs.select> 
                                {{-- <div class="pt-1"></div> --}}
                                <x-backend.inputs.select data-name="customer_segment_id" name="customerSegment" class="filterOnSelect" data-route="{{ route('admin.offerManagement') }}" :isRequired="false" label="Target Audience" :labelInline="false">
                                    <x-backend.inputs.select-option name="{{ translate('All Customers') }}" value="" />
                                    @foreach ($customerSegments as $segment)
                                        <x-backend.inputs.select-option name="{{ $segment->name }}" value="{{ $segment->id }}"
                                            selected="{{ Request::get('customer_segment_id') }}" />
                                    @endforeach 
                                </x-backend.inputs.select> 

                                <x-backend.inputs.textarea label="Message" name="message" placeholder="Type message here..."
                                    value="" :labelInline="false" />
                                    <span class="text-md text-muted">{{ translate('You can use {name} to add customer name in your message.') }}</span>    
                                    
                                <div class="flex mt-6 w-full">
                                    <x-backend.inputs.button class="w-full py-4 text-center justify-center mt-6" buttonText="Send offer to {{ $customerSegmentId ? $customerSegments->firstWhere('id', $customerSegmentId)->name . ' Customers': translate('All Customers') }}" type="submit" />
                                </div>
                            </div>
                        </form> 
                    </div>
                </div>
            </div>
        </div>
        <div class="md:col-span-1"> 
            <div class="card">
                <h4 class="card__title">{{ translate('Recipients') }} ({{ count($customers) }})</h4>
                <div class="card__content">
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                        @foreach ($customers as $customer)
                            @php
                                $customerSegment = getCustomerSegment($customer->orders(), $customerSegments);
                            @endphp
                            <div
                                class="flex items-center justify-between bg-background-primary-light rounded-md p-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ uploadedAsset($customer->avatar) }}" alt=""
                                        class="w-[70px] h-[80px] rounded-md"
                                        onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                    <div>
                                        <h5 class="font-bold text-sm">{{ $customer->name }}</h5>
                                        <p class="text-xs text-muted">{{ $customer->email }}</p>
                                    </div>
                                </div>
                                <div> 
                                    @if ($customerSegment[0]) 
                                        <span class="p-1 px-2 rounded-lg {{ $customerSegment[1] }}">
                                            {{  $customerSegment[0]->name}}
                                        </span>
                                    @else
                                        <span class="p-1 px-2 rounded-lg {{ $customerSegment[1] }}">
                                            {{ translate('No Segment') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div> 
        </div>
    </div>
@endsection
