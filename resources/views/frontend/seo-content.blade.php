{{-- Accessible fallback without JavaScript; React replaces it with the storefront on mount. --}}
<main style="max-width:1100px;margin:2rem auto;padding:1rem;font-family:system-ui;line-height:1.7">
    <nav><a href="{{ url('/') }}">{{ config('app.name') }}</a> · <a href="{{ url('/products') }}">Products</a> · <a href="{{ url('/blogs') }}">Beauty Journal</a></nav>
    <h1>{{ $seo['heading'] }}</h1>
    <p>{{ $seo['description'] }}</p>
    @if ($seo['body'])<article>{!! $seo['body'] !!}</article>@endif
    @if ($seo['items'])
        <ul>@foreach ($seo['items'] as $item)<li><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>@endforeach</ul>
    @endif
    @isset($seo['next'])<a href="{{ $seo['next'] }}">Next page</a>@endisset
    <address>
        @if(getSetting('homeFooterAddress'))<p>{{ getSetting('homeFooterAddress') }}</p>@endif
        @if(getSetting('homeFooterCustomerSupport'))<a href="tel:{{ preg_replace('/[^+\d]/', '', getSetting('homeFooterCustomerSupport')) }}">{{ getSetting('homeFooterCustomerSupport') }}</a>@endif
        @if(getSetting('homeFooterEmail'))<a href="mailto:{{ getSetting('homeFooterEmail') }}">{{ getSetting('homeFooterEmail') }}</a>@endif
    </address>
</main>
