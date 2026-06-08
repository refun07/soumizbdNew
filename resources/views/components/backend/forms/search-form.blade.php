@props(['count' => null,'orderIds'=>[]])
<div class="grow {{ $class }}">
    <div class="flex flex-col md:items-center md:flex-row items-stretch gap-2">
        {{ $slot }}
        <x-backend.inputs.search-input name="search" placeholder="{!! translate($placeholder) !!}" value="{!! $searchKey !!}"
            class="search-input" />
        @if($count>0 && request()->has('date_range'))
            <x-backend.inputs.button class="!bg-[#0B6623]" buttonText="Download all" type="button" onclick="downloadInvoices()" />
        @endif
    </div>

</div>

@section('scripts')
    <script type="text/javascript">
        "use strict";
        const url = new URL(window.location.href);

        // translate in one click
        function copyLocalizations() {
            window.location.href = url.pathname;
        }
        const ids = @json($orderIds);

        function downloadInvoices() {
            const redirect = `${url.pathname.replace(/\/orders$/, '')}/multiple-invoice-download/${ids.join(',')}`;
            window.open(redirect, '_blank');
        }

        // Make function global to ensure it's available
        window.copyLocalizations = copyLocalizations;
    </script>
@endsection
