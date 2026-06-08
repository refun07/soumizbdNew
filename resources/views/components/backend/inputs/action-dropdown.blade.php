@props(['label' => null])

<div class="ad-12b3e8cf relative inline-block {{ $class ?? '' }}" tabindex="0">
    <div class="ad-12b3e8cf__toggler whitespace-nowrap cursor-pointer min-w-[80px] h-10 flex items-center gap-3 bg-secondary/10 text-secondary rounded-md pl-3 pr-10 relative">
        <span>{{ $label }}</span>
    </div>

    <div class="ad-12b3e8cf__overlay fixed inset-0 z-[0] hidden"></div>

    <div class="ad-12b3e8cf__options absolute top-[calc(100%+10px)] bg-popover text-popover-foreground border border-border rounded-md min-w-max w-full p-[2px] shadow-theme translate-y-2 z-[1] opacity-0 invisible transition-transform duration-150">
        {{ $slot }}
    </div>
</div>

@once
<style>
    .ad-12b3e8cf__toggler:not(.no-arrow)::after {
        font-family: 'Font Awesome 6 Pro';
        content: '\f078';
        position: absolute;
        top: 0;
        right: 1rem;
        width: 0.75rem;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ad-12b3e8cf__option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        border-radius: 0.375rem;
        padding: 8px 0.75rem;
        color: var(--color-muted);
        transition: all 150ms;
        min-height: 32px;
    }
    @media (pointer: coarse) {
        .ad-12b3e8cf__option {
            padding: 10px 0.75rem;
            min-height: 44px;
        }
    }
    .ad-12b3e8cf__option:hover {
        color: var(--color-secondary);
        background-color: rgb(var(--color-secondary) / 0.1);
    }
    .ad-12b3e8cf:focus-within .ad-12b3e8cf__options {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .ad-12b3e8cf:focus-within .ad-12b3e8cf__overlay {
        display: block;
    }
    .ad-12b3e8cf__options--top {
        bottom: calc(100% + 10px);
        top: auto;
    }
</style>
<script>
    (function() {
        function closeDropdown(dd) {
            if (!dd) return;
            dd.blur();
            var toggler = dd.querySelector('.ad-12b3e8cf__toggler');
            if (toggler) toggler.blur();
        }

        document.addEventListener('focusin', function(e) {
            var dd = e.target.closest('.ad-12b3e8cf');
            if (!dd) return;
            var toggler = dd.querySelector('.ad-12b3e8cf__toggler');
            var options = dd.querySelector('.ad-12b3e8cf__options');
            if (!toggler || !options) return;
            var rect = toggler.getBoundingClientRect();
            var estimatedHeight = options.scrollHeight || 150;
            if (rect.bottom + estimatedHeight > window.innerHeight) {
                options.classList.add('ad-12b3e8cf__options--top');
            } else {
                options.classList.remove('ad-12b3e8cf__options--top');
            }
        });

        document.addEventListener('focusout', function(e) {
            var dd = e.target.closest('.ad-12b3e8cf');
            if (!dd) return;
            var options = dd.querySelector('.ad-12b3e8cf__options');
            if (options) options.classList.remove('ad-12b3e8cf__options--top');
        });

        document.addEventListener('touchstart', function(e) {
            var openDd = document.querySelector('.ad-12b3e8cf:focus-within');
            if (!openDd) return;
            if (e.target.closest('.ad-12b3e8cf') === openDd) return;
            closeDropdown(openDd);
        }, { passive: true });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.ad-12b3e8cf__overlay')) return;
            var openDd = e.target.closest('.ad-12b3e8cf') || document.querySelector('.ad-12b3e8cf:focus-within');
            if (openDd) closeDropdown(openDd);
        });
    })();
</script>
@endonce
