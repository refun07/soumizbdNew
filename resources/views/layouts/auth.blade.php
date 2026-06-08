<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>

    <!-- css -->
    <x-backend.inc.styles />
</head>

<body>
    <div class="h-100vh bg-[#eff7fd]">
        @yield('content')
    </div>

    <div class="overlay"></div>

    <!-- scripts -->
    {{-- <x-backend.inc.scripts /> --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>

    <script>
        "use strict";

        // ajax toast 
        function notifyMe(level, message) {
            if (level == 'danger') {
                level = 'error';
            }
            toastr.options = {
                closeButton: true,
                newestOnTop: false,
                progressBar: true,
                positionClass: "toast-top-right",
                preventDuplicates: false,
                onclick: null,
                showDuration: "3000",
                hideDuration: "1000",
                timeOut: "3000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
            };
            toastr[level](message);
        }

        // laravel flash toast messages 
        @foreach (session('flash_notification', collect())->toArray() as $message)
            notifyMe("{{ $message['level'] }}", "{{ $message['message'] }}");
        @endforeach
    </script>
</body>

</html>
