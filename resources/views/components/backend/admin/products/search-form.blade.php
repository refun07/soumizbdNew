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
    <p class="text-sm text-muted mt-2" data-product-search-status role="status" hidden></p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('.product-search-form form');
        if (form) {
            var input = form.querySelector('input[name="search"]');
            var status = document.querySelector('[data-product-search-status]');
            var timer;
            var pending;
            var sequence = 0;

            async function updateProducts(requestId) {
                var results = document.querySelector('[data-product-results]');
                if (!results || !input) return;
                var url = new URL(window.location.href);
                url.searchParams.delete('page');
                new FormData(form).forEach(function(value, name) {
                    if (String(value).trim()) url.searchParams.set(name, String(value).trim());
                    else url.searchParams.delete(name);
                });
                pending = new AbortController();
                results.setAttribute('aria-busy', 'true');
                status.hidden = false;
                status.textContent = @json(translate('Searching products...'));
                try {
                    var response = await fetch(url, {
                        signal: pending.signal,
                        headers: { 'Accept': 'text/html' },
                        credentials: 'same-origin',
                    });
                    if (!response.ok) throw new Error('Search failed');
                    var page = new DOMParser().parseFromString(await response.text(), 'text/html');
                    var next = page.querySelector('[data-product-results]');
                    if (!next) throw new Error('Search response unavailable');
                    if (requestId !== sequence) return;
                    results.replaceWith(next);
                    history.replaceState(null, '', url);

                    // Enhance the replacement rows without rebinding the entire admin page.
                    if (window.jQuery?.fn.footable) {
                        window.jQuery(next.querySelector('table')).footable({
                            breakpoints: { xs: 480, sm: 768, md: 992, lg: 1200, xl: 1400, '2xl': 1600 },
                            on: { 'ready.ft.table': function() {
                                if (typeof initConfirmModal === 'function') initConfirmModal();
                            } },
                        });
                    }
                    if (window.jQuery?.fn.select2) {
                        window.jQuery(next).find('select').select2({ minimumResultsForSearch: Infinity });
                    }
                    window.jQuery(next).find('.paginationForm').on('change', function() {
                        var target = new URL(window.location.href);
                        target.searchParams.set(this.name, this.value);
                        if (this.name === 'limit') target.searchParams.delete('page');
                        window.location.assign(target);
                    });
                    status.textContent = @json(translate('Product list updated'));
                } catch (error) {
                    if (error.name !== 'AbortError' && requestId === sequence) {
                        status.textContent = @json(translate('Could not update products. Press Enter to try again.'));
                    }
                } finally {
                    if (requestId === sequence) document.querySelector('[data-product-results]')?.removeAttribute('aria-busy');
                }
            }

            input?.addEventListener('input', function() {
                clearTimeout(timer);
                pending?.abort();
                var requestId = ++sequence;
                timer = setTimeout(function() { updateProducts(requestId); }, 300);
            });
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
