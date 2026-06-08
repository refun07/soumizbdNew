@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="hidden gap-2 md:flex-1 md:flex md:items-center md:justify-between">
            <div class="flex items-center gap-2">
                <form action="" method="GET">
                    <input type="hidden" name="page" value="{{ Request::get('page') ?? 1 }}" />
                    <x-backend.inputs.select name="limit" class="paginationForm" data-search="false">
                        <x-backend.inputs.select-option name="15 per page" value="15"
                            selected="{{ Request::get('limit') ?? 15 }}" />
                        <x-backend.inputs.select-option name="50 per page" value="50"
                            selected="{{ Request::get('limit') }}" />
                        <x-backend.inputs.select-option name="75 per page" value="75"
                            selected="{{ Request::get('limit') }}" />
                        <x-backend.inputs.select-option name="100 per page" value="100"
                            selected="{{ Request::get('limit') }}" />
                    </x-backend.inputs.select>
                </form>

                <form action="" method="GET">
                    <x-backend.inputs.text name="page" max="{{ $paginator->lastPage() }}" class="paginationForm"
                        placeholder="Go to: " />

                    <input type="hidden" name="limit" value="{{ Request::get('limit') ?? 15 }}" />
                </form>

            </div>
        </div>

        <div class="flex justify-between gap-4 max-md:flex-1">
            @if ($paginator->onFirstPage())
                <span class="button button--light capitalize cursor-default !text-muted">
                    <span>
                        <i class="far fa-chevron-left"></i>
                    </span>
                    <span>
                        {{ translate('Previous') }}
                    </span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}&limit={{ Request::get('limit') ?? 15 }}"
                    class="button button--light capitalize">
                    <span>
                        <i class="far fa-chevron-left"></i>
                    </span>
                    <span>
                        {{ translate('Previous') }}
                    </span>
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}&limit={{ Request::get('limit') ?? 15 }}"
                    class="button button--light capitalize">
                    <span>
                        {{ translate('Next') }}

                    </span>
                    <span>
                        <i class="far fa-chevron-right"></i>
                    </span>
                </a>
            @else
                <span class="button button--light capitalize cursor-default !text-muted">
                    <span>
                        {{ translate('Next') }}
                    </span>
                    <span>
                        <i class="far fa-chevron-right"></i>
                    </span>
                </span>
            @endif
        </div>
    </nav>
    <div class="mt-4">
        {{-- {{ dd($paginator) }} --}}
        <p class="text-sm text-muted leading-5">
            {!! __('Showing page') !!}
            <span class="font-medium">
                {{ $paginator->currentPage() }}
            </span>

            {!! __('of') !!}
            <span class="font-medium">{{ $paginator->lastPage() }}</span>
            {!! __('pages') !!}
        </p>
    </div>
@endif
