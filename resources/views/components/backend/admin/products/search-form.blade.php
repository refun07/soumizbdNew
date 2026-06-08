@props(['searchKey' => null, 'class' => ''])
<div class="product-search-form grow {{ $class }}">
    <form class="flex flex-col gap-3">
        <div class="flex flex-wrap items-center gap-2">
            {{ $buttonSlot ?? '' }}
            <div class="search-form relative flex-grow min-w-[200px]">
                <input type="text" name="search" value="{!! $searchKey !!}"
                    class="theme-input" placeholder="{{ translate('Search products...') }}"
                    autocomplete="off" />
                <span
                    class="product-search-icon text-primary dark:text-muted absolute top-0 right-0 h-full flex items-center justify-center px-3 cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{ $slot }}
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('.product-search-form form');
        if (form) {
            form.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.name === 'search') {
                    e.preventDefault();
                    form.submit();
                }
            });
        }

        var searchIcon = document.querySelector('.product-search-icon');
        if (searchIcon) {
            searchIcon.addEventListener('click', function(e) {
                e.preventDefault();
                var f = document.querySelector('.product-search-form form');
                if (f) {
                    f.submit();
                }
            });
        }
    });
</script>