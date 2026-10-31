/** Shared admin navigation behavior; never submits or changes business data. */
export function initAdminInteractions() {
    const sidebar = document.querySelector('#admin-sidebar');
    const triggers = [...document.querySelectorAll('.sidebar-toggler')];
    const overlay = document.querySelector('.overlay');
    const focusables = root => [...root.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex="0"]')].filter(node => node.getClientRects().length && getComputedStyle(node).visibility !== 'hidden' && !node.closest('[inert]'));
    let returnFocus;
    const syncSidebar = () => {
        if (!sidebar) return;
        const mobile = innerWidth < 1024;
        sidebar.classList.toggle('sidebar--mobile', mobile);
        // Desktop's compact width and flyout positioning must not leak into the drawer.
        if (mobile) {
            sidebar.classList.remove('in-active');
            sidebar.style.removeProperty('width');
        }
        const open = mobile ? sidebar.classList.contains('active') : !sidebar.classList.contains('in-active');
        sidebar.inert = mobile && !open;
        sidebar.setAttribute('aria-hidden', String(mobile && !open));
        triggers.forEach(button => {
            button.setAttribute('aria-expanded', String(open));
            button.setAttribute('aria-label', mobile ? (open ? 'Close navigation' : 'Open navigation') : (open ? 'Collapse navigation' : 'Expand navigation'));
        });
        sidebar.querySelectorAll('.has-submenu').forEach((item, index) => {
            const trigger = item.querySelector('.sidebar__item');
            const list = item.querySelector(':scope > ul');
            if (!trigger || !list) return;
            list.id ||= `admin-navigation-group-${index}`;
            trigger.setAttribute('role', 'button');
            trigger.setAttribute('aria-controls', list.id);
            trigger.setAttribute('aria-expanded', String(item.classList.contains('expanded')));
            // Collapsed desktop navigation reveals flyouts on focus as well as hover.
            list.inert = !item.classList.contains('expanded') && !(sidebar.classList.contains('in-active') && !mobile);
        });
    };
    const closeSidebar = () => {
        if (!sidebar || innerWidth >= 1024) return;
        sidebar.classList.remove('active');
        overlay?.classList.remove('active');
        syncSidebar();
        returnFocus?.focus({ preventScroll: true });
    };
    triggers.forEach(button => button.addEventListener('click', () => {
        if (innerWidth < 1024) {
            returnFocus = button;
            const open = !sidebar.classList.contains('active');
            sidebar.classList.toggle('active', open);
            overlay?.classList.toggle('active', open);
            syncSidebar();
            if (open) sidebar.querySelector('.admin-sidebar-close')?.focus({ preventScroll: true });
        } else {
            sidebar.classList.toggle('in-active');
            sidebar.style.width = sidebar.classList.contains('in-active') ? '90px' : '300px';
            syncSidebar();
        }
    }));
    sidebar?.querySelector('.admin-sidebar-close')?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    sidebar?.querySelectorAll('.has-submenu > .sidebar__item').forEach(button => {
        button.addEventListener('click', event => {
            event.preventDefault();
            button.parentElement.classList.toggle('expanded');
            syncSidebar();
        });
        button.addEventListener('keydown', event => {
            if (event.key === ' ') { event.preventDefault(); button.click(); }
        });
    });
    document.addEventListener('keydown', event => {
        if (innerWidth < 1024 && sidebar?.classList.contains('active')) {
            if (event.key === 'Escape') { event.preventDefault(); closeSidebar(); }
            if (event.key === 'Tab') {
                const items = focusables(sidebar), first = items[0], last = items.at(-1);
                if (!sidebar.contains(document.activeElement)) { event.preventDefault(); first?.focus(); }
                else if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
            }
        }
        if (event.key === 'Escape') {
            document.querySelectorAll('.admin-header-popover[data-open="true"]').forEach(popover => { closePopover(popover); popover.querySelector(':scope > button')?.focus(); });
            const search = document.querySelector('.mobile-search-box.active');
            if (search) { search.classList.remove('active'); document.querySelector('.toggle-mobile-search')?.setAttribute('aria-expanded','false'); document.querySelector('.toggle-mobile-search .fa-times')?.classList.replace('fa-times','fa-search'); document.querySelector('.toggle-mobile-search')?.focus(); }
        }
    });
    window.addEventListener('resize', () => {
        if (innerWidth >= 1024) { sidebar?.classList.remove('active'); overlay?.classList.remove('active'); }
        syncSidebar();
    });
    syncSidebar();
    document.addEventListener('keydown', event => {
        const trigger = event.target.closest('[role="button"][data-micromodal-trigger],.option-dropdown__option[role="button"]');
        if (trigger && ['Enter', ' '].includes(event.key)) { event.preventDefault(); trigger.click(); }
    });

    const search = document.querySelector('.mobile-search-box');
    const searchTrigger = document.querySelector('.toggle-mobile-search');
    if (search && searchTrigger) {
        searchTrigger.setAttribute('aria-controls', 'admin-mobile-search');
        searchTrigger.setAttribute('aria-expanded', 'false');
        search.inert = true;
        searchTrigger.addEventListener('click', () => {
            const open = !search.classList.contains('active');
            search.classList.toggle('active', open);
            search.inert = !open;
            searchTrigger.setAttribute('aria-expanded', String(open));
            searchTrigger.querySelector('i')?.classList.replace(open ? 'fa-search' : 'fa-times', open ? 'fa-times' : 'fa-search');
            if (open) {
                requestAnimationFrame(() => search.querySelector('input')?.focus({ preventScroll: true }));
                window.setTimeout(() => { if (search.classList.contains('active')) search.querySelector('input')?.focus({ preventScroll: true }); }, 200);
            }
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') search.inert = !search.classList.contains('active');
            if (event.key === 'Tab' && search.classList.contains('active')) {
                const items = [...focusables(search), searchTrigger];
                const index = items.indexOf(document.activeElement);
                if (index < 0 || (!event.shiftKey && index === items.length - 1) || (event.shiftKey && index === 0)) {
                    event.preventDefault(); (event.shiftKey ? items.at(-1) : items[0])?.focus();
                }
            }
        });
    }

    // Normalize legacy div-based dropdown triggers, including React-inserted controls.
    const enhanceDropdowns = () => document.querySelectorAll('.option-dropdown:not([data-accessible])').forEach((dropdown, index) => {
        dropdown.dataset.accessible = 'true';
        const trigger = dropdown.querySelector('.option-dropdown__toggler');
        const options = dropdown.querySelector('.option-dropdown__options');
        if (!trigger || !options) return;
        dropdown.tabIndex = -1;
        if (trigger.tagName !== 'BUTTON') { trigger.tabIndex = 0; trigger.setAttribute('role', 'button'); }
        trigger.setAttribute('aria-expanded', String(dropdown.classList.contains('option-dropdown--open')));
        options.id ||= `admin-options-${Date.now()}-${index}`;
        trigger.setAttribute('aria-controls', options.id);
        const text = trigger.textContent?.replace(/[\uE000-\uF8FF]/g, '').trim();
        if (!text && !trigger.hasAttribute('aria-label')) trigger.setAttribute('aria-label', 'More actions');
        options.querySelectorAll('.option-dropdown__option').forEach(option => {
            if (!['A', 'BUTTON', 'INPUT'].includes(option.tagName)) { option.tabIndex = 0; option.setAttribute('role', 'button'); }
        });
    });
    enhanceDropdowns();
    new MutationObserver(enhanceDropdowns).observe(document.body, { childList: true, subtree: true });

    function closePopover(popover) {
        popover.dataset.open = 'false';
        popover.querySelector(':scope > button')?.setAttribute('aria-expanded', 'false');
    }
    document.querySelectorAll('.admin-header-popover').forEach(popover => {
        const trigger = popover.querySelector(':scope > button');
        trigger?.setAttribute('aria-expanded', 'false');
        trigger?.addEventListener('click', () => {
            const open = popover.dataset.open !== 'true';
            document.querySelectorAll('.admin-header-popover').forEach(closePopover);
            popover.dataset.open = String(open);
            trigger.setAttribute('aria-expanded', String(open));
        });
        popover.addEventListener('focusout', event => { if (!popover.contains(event.relatedTarget)) closePopover(popover); });
        popover.addEventListener('keydown', event => { if (event.key === 'Escape') { closePopover(popover); trigger.focus(); } });
    });
    document.addEventListener('click', event => {
        document.querySelectorAll('.admin-header-popover').forEach(popover => { if (!popover.contains(event.target)) closePopover(popover); });
    });
}
