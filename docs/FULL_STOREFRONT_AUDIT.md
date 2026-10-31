# Storefront audit — 17 October 2026

Reviewed 158 local storefront routes at 375 × 812 and 1440 × 900: all 70 imported product pages, 42 categories, 17 articles, and 29 listing, account, commerce, support and error routes. Checks waited for the actual product/category/article heading rather than treating a loading shell as a completed page.

All 316 final route checks passed without page-level horizontal overflow, a blank content area, an error boundary, or a loaded image with a broken source. Core cart/checkout and the six authenticated account screens were also checked at 320px. Screenshots and per-route evidence are in `artifacts/full-storefront-audit/`.

## Fixes

- Missing CMS pages show the not-found page instead of remaining on Loading. Other request failures offer retry.
- Payment opened without an order shows a useful return-to-cart screen and does not submit an empty payment request. Valid payment transitions retain their existing form flow and now show progress and a manual continuation control.
- Guest account routes show a sign-in action instead of silently navigating home during render.
- Shared search forms encode special characters correctly, clear searches on submission, and clean up native event listeners. Verified `oil & cream` remains one complete query in both header and brand search.
- Order tracking has a heading, a correctly associated input label, a 44px search action and inline validation. Existing test order 10006 loads successfully. Order details label and format the creation timestamp as Order Date.
- Quick-view and authentication close controls are named and enlarged. Filters have a named 44px close control. Dialogs have accessible roles, Tab wrapping, focus restoration and Escape dismissal.
- Sign-in labels match their input IDs, email/password validation errors display beside their fields, autocomplete is supplied, and the apostrophe in the registration prompt renders correctly.

## Functional checks

- Existing order 10006 displays in order details/tracking; no new order was submitted in this audit.
- One BDT 500 cart item adds and removes correctly. Its purchase limit disables further increase. At 320px and desktop, totals are BDT 570 with inside-Dhaka shipping and BDT 650 outside. An incomplete checkout focuses the missing name. The audit item was removed afterward.
- Temporary local customer 6 verified login, dashboard, orders, wishlist, chats, reviews, settings and logout at 320px and desktop. Wishlist save, persistence after navigation and removal passed. Tokens were revoked and the fixture was soft-deleted. Order count stayed at seven before and after.
- Quick-view Tab/Shift+Tab wrapping and Escape close passed. Guest sign-in empty submission displays email/password errors.
- Existing SEO verification passed all 129 imported pages, their 129 legacy redirects, 135 sitemap URLs, metadata, canonical URLs and missing/private-page behavior.
- Production build and `git diff --check` passed. The two chunk recovery tests passed. TypeScript retains 87 existing baseline diagnostics; this audit introduced no new diagnostic signatures.

## Limits

Online gateway settlement, production SMS/OTP, email delivery and real customer communications were not exercised. No active campaign-detail or published CMS records were available; their available empty/missing states were checked. Signed-in account views used an empty test account, with the populated wishlist tested separately. The existing local test order supplied populated order/tracking coverage.
