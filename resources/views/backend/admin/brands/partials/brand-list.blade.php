<div class="grid xl:grid-cols-2 gap-3 lg:gap-5">
    @forelse ($brands as $brand)
        <div class="py-3 px-4 bg-background-primary-light rounded-md flex items-center gap-3.5 relative">
            <img src="{{ uploadedAsset($brand->thumbnail_image) }}" alt=""
                class="w-[70px] h-[80px] shrink-0 rounded-md object-cover"
                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">

            <div class="grow min-w-0 pr-12">
                <h4 class="font-bold truncate">{{ $brand->collectTranslation('name') }}</h4>
                <p class="text-xs mt-1 whitespace-nowrap">
                    {{ translate('Total Products') }}:
                    <span class="text-muted ml-0.5">{{ $brand->products_count }}</span>
                </p>
                @if ($brand->collections->isNotEmpty())
                    <div class="mt-1.5 flex flex-wrap gap-1">
                        @foreach ($brand->collections as $collection)
                            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[11px] text-primary">
                                {{ $collection->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex gap-3 absolute top-3 right-3">
                @can('edit_brands')
                    <a class="text-stone-300 hover:text-stone-500"
                        href="{{ route('admin.brands.edit', ['brand' => $brand->id, 'lang_key' => config('app.default_language')]) }}&translate">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                @endcan

                @can('delete_brands')
                    <a class="text-red-400 hover:text-rose-600 confirm-modal" href="javascript:void(0);"
                        data-href="{{ route('admin.brands.destroy', $brand->id) }}"
                        data-title="{{ translate('Are you sure you want to delete this item?') }}"
                        data-text="{{ translate('All data related to this may get deleted.') }}"
                        data-method="DELETE" data-micromodal-trigger="confirm-modal">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                @endcan
            </div>
        </div>
    @empty
        <p class="xl:col-span-2 py-6 text-center text-muted">{{ translate('No brands found') }}</p>
    @endforelse
</div>
