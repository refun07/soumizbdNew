@extends('layouts.admin')

@section('title')
    {{ translate('Customer List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Customer Management') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Customer Management') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5">
            {{-- customer segment --}}
            <div class="card theme-table mb-6">
                <div
                    class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3"> 
                    <div>{{ translate("Manage Customer Segments") }}</div> 
                    <div></div>
                </div> 

                <div class="p-3 md:p-9 !pt-0">
                    <div class="flex flex-col gap-3 customerSegmentViews">
                        @include('backend.admin.customers.customer-segment',['segments'=> $segments])
                    </div>
                    <x-backend.inputs.button buttonText="Add Segment" class="mt-4 addCustomerSegment" type="button" /> 
                </div>
            </div>
            
            {{-- customers --}}
            <div class="card theme-table">
                <div
                    class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

                    <div class="flex items-center gap-3">
                        <div>{{ translate("Customer List") }}</div>
                        @can('create_customers')
                            <a class="text-primary bg-primary/10 px-3 py-1 rounded-md text-sm font-medium hover:bg-primary/20 transition-colors cursor-pointer"
                                data-micromodal-trigger="customer-create-modal"
                                href="javascript:void(0);">
                                <i class="fa-solid fa-plus mr-1"></i>{{ translate('Add Customer') }}
                            </a>
                            <a class="text-success bg-success/10 px-3 py-1 rounded-md text-sm font-medium hover:bg-success/20 transition-colors cursor-pointer"
                                data-micromodal-trigger="customer-import-modal"
                                href="javascript:void(0);">
                                <i class="fa-solid fa-file-import mr-1"></i>{{ translate('Import') }}
                            </a>
                        @endcan
                        @can('view_customers')
                            @php $searchParam = request('search') ? '?search=' . urlencode(request('search')) : ''; @endphp
                            <a href="{{ route('admin.customers.export') }}{{ $searchParam }}"
                                class="text-info bg-info/10 px-3 py-1 rounded-md text-sm font-medium hover:bg-info/20 transition-colors cursor-pointer">
                                <i class="fa-solid fa-file-export mr-1"></i>{{ translate('Export') }}
                            </a>
                        @endcan
                    </div>

                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[480px]"
                        placeholder="Type name/email/phone & hit enter">

                        <x-backend.inputs.select name="sortBy" class="filterSelect" :isRequired="false">
                            <x-backend.inputs.select-option name="{{ translate('Orders') }}" value=""
                                selected="{{ Request::get('sortBy') }}" />
                            <x-backend.inputs.select-option name="{{ translate('$ - High to Low') }}"
                                value="amountHighToLow" selected="{{ Request::get('sortBy') }}" />
                            <x-backend.inputs.select-option name="{{ translate('$ - Low to High') }}"
                                value="amountLowToHigh" selected="{{ Request::get('sortBy') }}" />
                            <x-backend.inputs.select-option name="{{ translate('N - High to Low') }}" value="qtyHighToLow"
                                selected="{{ Request::get('sortBy') }}" />
                            <x-backend.inputs.select-option name="{{ translate('N - Low to High') }}" value="qtyLowToHigh"
                                selected="{{ Request::get('sortBy') }}" />

                        </x-backend.inputs.select>
                    </x-backend.forms.search-form>
                </div>

                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th data-breakpoints="xs sm">
                                #
                            </th>
                            <th>
                                {{ translate('Name') }}
                            </th> 
                            
                            <th>
                                {{ translate('Segment') }}
                            </th>  
                            <th  data-breakpoints="xs sm">
                                {{ translate('Purchase Value') }}
                            </th>   
                            <th  data-breakpoints="xs sm">
                                {{ translate('Last Purchase') }}
                            </th>  
                            <th  data-breakpoints="xs sm">
                                {{ translate('Assigned To') }}
                            </th  data-breakpoints="xs sm">  
                            <th  data-breakpoints="xs sm">
                                {{ translate('Reorder Reminder') }}
                            </th>  
                            <th  data-breakpoints="xs sm">
                                {{ translate('Next Follow-up') }}
                            </th>  
                            <th  data-breakpoints="xs sm">
                                {{ translate('Banned') }}
                            </th>
                            <th data-breakpoints="xs sm" class="text-end">
                                {{ translate('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $key => $customer) 
                            @php
                                $customerSegment = getCustomerSegment($customer->orders(), $segments);
                                $firstOrder      = $customer->orders()->latest()->first();
                                $phone           = $customer->phone;
                                if(strpos($phone, '+') !== 0){
                                    $phone = +88 . $phone;
                                }
                            @endphp
                            <tr>
                                <td>{{ $key + 1 + ($customers->currentPage() - 1) * $customers->perPage() }}</td>

                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="max-xs:hidden">
                                            <img src="{{ uploadedAsset($customer->avatar) }}" alt=""
                                                class="w-[70px] h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                        </div>
                                        <div class="">
                                            <div class=" line-clamp-2">
                                                {{ $customer->name }}
                                            </div>
                                            <div class="text-muted line-clamp-2">
                                                {{ $customer->email }}
                                            </div>
                                            
                                            <div class="text-muted line-clamp-2">
                                                {{ $customer->phone ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td> 
                                <td>
                                    @if ($customerSegment[0]) 
                                        <span class="p-1 px-2 rounded-lg {{ $customerSegment[1] }}">
                                            {{  $customerSegment[0]->name}}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td> 
                                <td>
                                    <div class="text-dark underline line-clamp-2">
                                        <a href="{{ route('admin.orders.index') }}?customerId={{ $customer->id }}">
                                            {{ formatPrice( $customerSegment[2]) }}
                                            ({{ $customer->orders()->count() }})
                                        </a>
                                    </div>
                                </td>

                                <td>
                                    {{ $firstOrder != null ? $firstOrder->created_at->format('Y-m-d') : '-' }}
                                </td>
                                <td> 
                                    {{ $customer->assignedTo ? $customer->assignedTo->name : '-' }}
                                </td>
                                <td>
                                    {{ $customer->reorder_reminder_date != null ? \Carbon\Carbon::parse($customer->reorder_reminder_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td> 
                                    {{ $customer->next_follow_up_date != null ? \Carbon\Carbon::parse($customer->next_follow_up_date)->format('Y-m-d') : '-' }}
                                </td>

                                <td>
                                    @can('edit_customers')
                                        <x-backend.inputs.checkbox toggler="true"
                                            data-route="{{ route('admin.customers.toggleBan') }}" variant="danger"
                                            name="isActiveCheckbox" value="{{ $customer->id }}"
                                            data-status="{{ $customer->is_banned }}"
                                            isChecked="{{ (int) $customer->is_banned == 1 }}" />
                                    @endcan
                                </td>

                                <td class="text-end flex items-center gap-3">
                                    @php
                                        $logNotes = $customer->customerNoteLogs;
                                        $html = '';
                                        foreach($logNotes as $note){ 
                                            $html .= '<div class="grid grid-cols-12 gap-4 mb-2 activity-log-item">
                                                        <div class="col-span-4">
                                                            <label class="theme-input-label pt-0">'. translate('Type') .'</label>
                                                            <div class="theme-input-wrapper">
                                                                <select class="theme-input h-auto p-3" name="types[]">
                                                                    <option value="note"'. ($note->type == "note" ? " selected" : "") .'>'. translate('Note') .'</option> 
                                                                    <option value="call_recording"'. ($note->type == "call_recording" ? " selected" : "") .'>'. translate('Call Recording') .'</option> 
                                                                    <option value="whatsapp_messaging"'. ($note->type == "whatsapp_messaging" ? " selected" : "") .'>'. translate('WhatsApp Messaging') .'</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-span-8">
                                                            <label class="theme-input-label pt-0">'. translate('Details') .'</label>
                                                            <textarea class="theme-input theme-textarea" name="details[]" placeholder="'. translate('Type few words...') .'" rows="6">'. htmlspecialchars($note->note) .'</textarea>
                                                        </div>
                                                    </div>';
                                        }
                                    @endphp
                                    <a class="text-stone-300 hover:text-stone-500 me-1 customer-assignment-modal-trigger"
                                        data-micromodal-trigger="customer-assignment-modal"
                                        data-name="{{ $customer->name }}"
                                        data-id="{{ $customer->id }}"
                                        data-reminder-date="{{ $customer->reorder_reminder_date ? \Carbon\Carbon::parse($customer->reorder_reminder_date)->format('Y-m-d\TH:i') : '' }}"
                                        data-next-date="{{ $customer->next_follow_up_date ? \Carbon\Carbon::parse($customer->next_follow_up_date)->format('Y-m-d\TH:i') : '' }}"
                                        data-assigned-to="{{ $customer->assigned_to }}"
                                        data-status-note="{{ $customer->status_note }}"
                                        data-html-notes='{!! $html !!}'
                                        href="javascript:void(0);">
                                        <i class="fa-regular fa-pencil text-md"></i>
                                    </a>

                                    @if ($customer->phone)
                                        <a class="text-blue-500 hover:text-blue-600 me-1"
                                            href="tel:{{ $phone }}">
                                            <i class="fa-regular fa-phone text-md"></i>
                                        </a>
                                        <a target="_blank" class="text-green-500 hover:text-green-600"
                                            href="https://wa.me/{{$phone}}">
                                            <i class="fa-regular fa-brands fa-whatsapp text-md"></i>
                                        </a>
                                    @endif

                                    @can('delete_customers')
                                        <a href="javascript:void(0);"
                                            data-href="{{ route('admin.customers.destroy', $customer->id) }}"
                                            data-title="{{ translate('Are you sure want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="DELETE" data-micromodal-trigger="confirm-modal"
                                            class="confirm-modal text-red-500 text-sm ms-2">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card__footer">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add Customer Modal --}}
    @can('create_customers')
        @include('components.backend.modals.customer-create')
    @endcan

    {{-- Import Customer Modal --}}
    @can('create_customers')
        <div class="modal micromodal-slide" id="customer-import-modal" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="customer-import-modal-title">
                <header class="modal__header">
                    <h2 class="modal__title">
                        {{ translate('Import Customers') }}
                    </h2>
                </header>
                <main class="modal__content w-full">
                    <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div id="importResult" class="hidden mb-3 p-3 rounded-lg text-sm"></div>
                        <div class="space-y-4">
                            <div>
                                <label class="theme-input-label pt-0">{{ translate('CSV File') }}</label>
                                <input type="file" name="csv_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" accept=".csv" required>
                                <small class="text-muted">{{ translate('Max size: 10MB') }}</small>
                            </div>
                            <div class="bg-primary/5 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium">{{ translate('CSV Format Guide') }}</h4>
                                    <a href="{{ route('admin.customers.sampleCsv') }}" class="text-primary hover:underline text-sm">
                                        <i class="fa-solid fa-download mr-1"></i>{{ translate('Download Sample CSV') }}
                                    </a>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center text-xs">1</span>
                                        <span class="font-medium">{{ translate('name') }}</span>
                                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded">{{ translate('Required') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center text-xs">2</span>
                                        <span class="font-medium">{{ translate('email') }}</span>
                                        <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded">{{ translate('Optional') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center text-xs">3</span>
                                        <span class="font-medium">{{ translate('phone') }}</span>
                                        <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded">{{ translate('Optional') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center text-xs">4</span>
                                        <span class="font-medium">{{ translate('password') }}</span>
                                        <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded">{{ translate('Optional') }}</span>
                                        <span class="text-muted text-xs ml-1">{{ translate('(default: 123456)') }}</span>
                                    </div>
                                </div>
                                <p class="text-xs text-muted mt-3">{{ translate('Note: Either email or phone is required for each customer.') }}</p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <x-backend.inputs.button type="button" variant="secondary" buttonText="Cancel" data-micromodal-close="" />
                            <x-backend.inputs.button type="submit" buttonText="Import" id="importBtn" />
                        </div>
                    </form>
                </main>
            </div>
        </div>
        <script>
            document.getElementById('importForm').addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var btn = document.getElementById('importBtn');
                var result = document.getElementById('importResult');

                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>{{ translate("Processing") }}';

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '{{ translate("Import") }}';

                    if (data.success) {
                        result.classList.remove('hidden', 'bg-red-100', 'text-red-700');
                        result.classList.add('bg-green-100', 'text-green-700');
                        result.innerHTML = '<strong>{{ translate("Success!") }}</strong><br>' +
                            '{{ translate("Created: ") }}' + data.result.created + '<br>' +
                            '{{ translate("Updated: ") }}' + data.result.updated;

                        if (data.result.errors && data.result.errors.length > 0) {
                            result.innerHTML += '<br><br><strong>{{ translate("Errors (showing first 10):") }}</strong><br>' + data.result.errors.join('<br>');
                            var moreErrors = data.result.total_errors - data.result.errors.length;
                            if (moreErrors > 0) {
                                result.innerHTML += '<br><span class="text-muted">{{ translate("And ") }}' + moreErrors + '{{ translate(" more error(s). Please fix and re-upload.") }}</span>';
                            }
                        }

                        var countdown = 5;
                        result.innerHTML += '<br><br><span class="countdown-text">{{ translate("Modal will auto close in ") }}' + countdown + '{{ translate(" seconds") }}</span>';

                        var countdownInterval = setInterval(function() {
                            countdown--;
                            var countdownEl = result.querySelector('.countdown-text');
                            if (countdownEl) {
                                countdownEl.textContent = '{{ translate("Modal will auto close in ") }}' + countdown + '{{ translate(" seconds") }}';
                            }
                            if (countdown <= 0) {
                                clearInterval(countdownInterval);
                                MicroModal.close('customer-import-modal');
                                window.location.reload();
                            }
                        }, 1000);
                    } else {
                        result.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                        result.classList.add('bg-red-100', 'text-red-700');
                        result.innerHTML = data.message;
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = '{{ translate("Import") }}';
                    result.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                    result.classList.add('bg-red-100', 'text-red-700');
                    result.innerHTML = '{{ translate("An error occurred during import") }}';
                });
            });
        </script>
    @endcan
@endsection