<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} Installation</title>

    {{-- <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}" /> --}}

    <!-- Fonts -->
    {{-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" /> --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&family=Sacramento&display=swap"
        rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/installation/index.css', 'resources/installation/index.tsx'])
</head>

<body class="antialiased">
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="app"></div>
</body>

</html>
