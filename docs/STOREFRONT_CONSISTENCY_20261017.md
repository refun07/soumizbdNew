# Storefront consistency audit — 17 October 2026

## Coverage

158 local storefront routes checked at 375px and 1440px (316 checks): 70 product pages, 42 categories, 17 articles and 29 commerce, brand, account-entry, support, listing and error routes. No final check had page-level overflow, an application error or a loaded broken image. The standalone server-rendered order-success route was checked separately from the React storefront; its results are included in the 316 total. Shop and campaign loading placeholders were explicitly allowed to settle and rechecked at both widths.

Six authenticated customer screens (dashboard, orders, wishlist, chats, reviews, settings) were also checked at 320px and desktop using a disposable local customer. Populated cart/checkout checks passed at 320px and desktop, including a ৳500 product plus ৳70 inside-Dhaka delivery = ৳570. The cart item was removed, the customer logged out and soft-deleted, and its tokens revoked. Local order count remained eight. No order/payment/customer message was submitted.

## Changes

- Consistent 1180px content width, adaptive gutters and nested account-card padding.
- Responsive page heading/breadcrumb treatment, complete breadcrumb text and appropriate heading semantics.
- Shared pill buttons and 46px inputs with consistent colors, contrast and focus treatment.
- Consistent directory and journal cards, safe missing-image fallback, readable brand titles.
- Removed legacy gold underlines from common page headings.
- Article hero reduced to a readable height, explicit loading/retry/missing-article states, named 44px sharing controls.
- Order confirmation uses the existing compiled stylesheet instead of the runtime Tailwind CDN, and matching green, typography and rounded controls.

Shared product-card purchase logic, prices/stock, homepage content settings and checkout rules were retained.

## Validation and deployment

Production build and `git diff --check` passed. TypeScript retains the same 87 existing diagnostics; no new diagnostic signatures compared with the previous baseline.

Deployed to https://test.soumisbangladesh.com through cPanel, updating public build files and 15 private source/template files. Update ZIP SHA-256: `7142599a765ae505478f5f830aac523d1b160eda6ed560f3028d28df81697eb0`. Previous hashed assets and the previous private release archives were retained for rollback/open sessions. No server database changes.

Live verification covered catalog, Health Care category, brands, blog listing/article, tracking, cart and checkout at 375px and 1440px (16 checks), all without page overflow. Existing live cart items were preserved. Signed-in account and populated confirmation coverage used local fixtures; no live test order was created. Active campaign details and published CMS content were unavailable, so existing empty/missing states were reviewed. SMTP/OTP/payment settlement were not part of this visual audit. Admin pages are outside this storefront audit.

Evidence: `artifacts/storefront-consistency/` includes route results, settled follow-ups, account/commerce results, screenshots and release metadata.
