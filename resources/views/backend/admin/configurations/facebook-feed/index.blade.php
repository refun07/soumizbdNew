@extends('layouts.admin')

@section('title')
    {{ translate('Facebook Feed') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    {{-- breadcrumb starts --}}
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-brands fa-facebook"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Facebook Feed') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold">{{ translate('Configurations') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Facebook Feed') }}</p>
        </div>
    </div>
    {{-- breadcrumb ends --}}

    {{-- Feed Status Card --}}
    <div class="card max-w-[900px]">
        <h4 class="card__title">{{ translate('Feed Status') }}</h4>

        <div class="card__content">
            <div class="space-y-6">
                {{-- Status Indicator --}}
                <div class="flex items-center gap-4 p-4 rounded-lg {{ $feedInfo['exists'] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20' }}">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $feedInfo['exists'] ? 'bg-green-100 dark:bg-green-800' : 'bg-yellow-100 dark:bg-yellow-800' }}">
                        <i class="fa-solid {{ $feedInfo['exists'] ? 'fa-check text-green-600 dark:text-green-400' : 'fa-triangle-exclamation text-yellow-600 dark:text-yellow-400' }} text-xl"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-lg">
                            {{ $feedInfo['exists'] ? translate('Feed is Ready') : translate('Feed Not Generated') }}
                        </h5>
                        <p class="text-muted text-sm">
                            {{ $feedInfo['exists'] 
                                ? translate('Your Facebook product catalog feed is ready to use') 
                                : translate('Generate a feed to sync your products with Facebook') }}
                        </p>
                    </div>
                </div>

                @if($feedInfo['exists'])
                    {{-- Feed Information --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-muted/30 rounded-lg">
                            <p class="text-muted text-sm mb-1">{{ translate('Last Generated') }}</p>
                            <p class="font-semibold">{{ $feedInfo['lastGenerated'] }}</p>
                        </div>
                        <div class="p-4 bg-muted/30 rounded-lg">
                            <p class="text-muted text-sm mb-1">{{ translate('Products') }}</p>
                            <p class="font-semibold">{{ number_format($feedInfo['productCount']) }}</p>
                        </div>
                        <div class="p-4 bg-muted/30 rounded-lg">
                            <p class="text-muted text-sm mb-1">{{ translate('File Size') }}</p>
                            <p class="font-semibold">{{ $feedInfo['fileSize'] }}</p>
                        </div>
                    </div>

                    {{-- Public URL --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-muted text-sm mb-2">{{ translate('Public Feed URL') }}</p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 bg-white dark:bg-gray-800 px-3 py-2 rounded text-sm break-all">
                                {{ url('/facebook-feed') }}
                            </code>
                            <button onclick="copyToClipboard('{{ url('/facebook-feed') }}')" 
                                    class="px-3 py-2 bg-primary text-white rounded hover:bg-primary/90 transition-colors"
                                    title="{{ translate('Copy URL') }}">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                        <p class="text-xs text-muted mt-2">
                            {{ translate('Use this URL in your Facebook Business Manager to sync products') }}
                        </p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-3 pt-4 border-t">
                    <form action="{{ route('admin.facebook-feed.generate') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-rotate"></i>
                            {{ translate('Generate Feed') }}
                        </button>
                    </form>

                    @if($feedInfo['exists'])
                        <a href="{{ route('admin.facebook-feed.download') }}" 
                           class="px-4 py-2 bg-success text-white rounded-lg hover:bg-success/90 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-download"></i>
                            {{ translate('Download CSV') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Instructions Card --}}
    <div class="card max-w-[900px] mt-5">
        <h4 class="card__title">{{ translate('How to Use') }}</h4>

        <div class="card__content">
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="text-primary font-semibold text-sm">1</span>
                    </div>
                    <div>
                        <h6 class="font-semibold">{{ translate('Generate the Feed') }}</h6>
                        <p class="text-muted text-sm">{{ translate('Click "Generate Feed" to create/update your product catalog CSV file.') }}</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="text-primary font-semibold text-sm">2</span>
                    </div>
                    <div>
                        <h6 class="font-semibold">{{ translate('Copy the Feed URL') }}</h6>
                        <p class="text-muted text-sm">{{ translate('Copy the public URL shown above or download the CSV file.') }}</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="text-primary font-semibold text-sm">3</span>
                    </div>
                    <div>
                        <h6 class="font-semibold">{{ translate('Configure in Facebook') }}</h6>
                        <p class="text-muted text-sm">{{ translate('Go to Facebook Business Manager → Commerce Manager → Catalog → Data Sources. Add a new data feed source and paste the URL.') }}</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="text-primary font-semibold text-sm">4</span>
                    </div>
                    <div>
                        <h6 class="font-semibold">{{ translate('Set Up Auto-Sync') }}</h6>
                        <p class="text-muted text-sm">{{ translate('In Facebook, configure the feed to automatically update daily to keep your catalog in sync with your store.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            const message = @json(translate('URL copied to clipboard'));
            if (typeof flash !== 'undefined') {
                flash(message, 'success');
            } else {
                alert(message);
            }
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endpush
