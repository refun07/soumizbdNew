# Mobile storefront layout audit

Reviewed the available local storefront routes on 2026-10-16, including all 70 product detail pages, 42 category pages and 17 blog articles at 390px. Also checked the homepage, catalog, brand listing, journal, offers, empty states, cart, checkout, order tracking, support, shop pages and error page. Checked representative layouts at 320px, tablet width and desktop. A temporary local customer was used to inspect the six signed-in account screens and one populated cart/checkout.

Changes:

- Checkout now has explicit shrinking grid tracks and stacks summary items into readable mobile cards. Prices, quantity and subtotal remain visible, and quantity controls have larger touch targets. Desktop keeps its table layout.
- Account navigation and logout are accessible on mobile. Account skeletons and content shrink within the viewport.
- Header stacking remains above hero artwork and below modal backdrops/cart/filter drawers.
- Shared form inputs have 44px mobile height, 16px text and a zero minimum width.
- Empty brand/shop messages span their actual grids instead of creating extra columns.
- Shop/brand header skeletons fit narrow screens. Missing shop banners render a neutral background; the single-vendor shop uses the configured logo when its own logo is unset. Shop headings/actions/tabs wrap or scroll within their own area.

Evidence and route coverage: `artifacts/mobile-layout-audit/`. Checks included DOM bounds and rendered screenshots, actual cart/filter drawer opening, account navigation, and populated checkout at 320px. The test item's unit price and total stayed at BDT 500 for quantity 1. No order was submitted.

Scope limits: the local database has no historical orders, active campaign detail records or published CMS pages, so record-specific order/payment/success states could not be reviewed with real data. Guest/authentication states and available empty screens were checked. This audit concerns the storefront, not the separate admin interface. The production build passed; TypeScript retains the existing 87 errors with no new errors from these changes.
