<div class="grid xl:grid-cols-2 gap-3 lg:gap-5">
    @forelse ($collections as $collection)
        <div class="py-3 px-4 bg-background-primary-light rounded-md flex items-center gap-3.5 relative">
            <img src="{{ uploadedAsset($collection->image) }}" alt=""
                class="w-[70px] h-[80px] shrink-0 rounded-md object-cover"
                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">

            <div class="grow min-w-0 pr-12">
                <h4 class="font-bold truncate">{{ $collection->name }}</h4>
                <p class="text-xs mt-1 whitespace-nowrap">
                    {{ translate('Total Products') }}:
                    <span class="text-muted ml-0.5">{{ $collection->products_count }}</span>
                    <span class="mx-1.5 text-muted">&middot;</span>
                    {{ translate('Sort Order') }}:
                    <span class="text-muted ml-0.5">{{ $collection->sort_order }}</span>
                </p>
                @if ($collection->brands->isNotEmpty())
                    <div class="mt-1.5 flex flex-wrap gap-1">
                        @foreach ($collection->brands as $brand)
                            <span class="rounded-full bg-secondary/10 px-2 py-0.5 text-[11px] text-secondary">
                                {{ $brand->collectTranslation('name') }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs mt-1 text-muted">{{ translate('Not added to any brand yet') }}</p>
                @endif
            </div>

            <div class="flex gap-3 absolute top-3 right-3">
                @can('edit_brands')
                    <a class="text-stone-300 hover:text-stone-500"
                        href="{{ route('admin.collections.edit', $collection->id) }}">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                @endcan

                @can('delete_brands')
                    <a class="text-red-400 hover:text-rose-600 confirm-modal" href="javascript:void(0);"
                        data-href="{{ route('admin.collections.destroy', $collection->id) }}"
                        data-title="{{ translate('Are you sure you want to delete this item?') }}"
                        data-text="{{ translate('Brands and products will be removed from this collection.') }}"
                        data-method="DELETE" data-micromodal-trigger="confirm-modal">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                @endcan
            </div>
        </div>
    @empty
        <p class="xl:col-span-2 py-6 text-center text-muted">{{ translate('No collections yet') }}</p>
    @endforelse
</div>
