# Mobile larger-font audit — 17 October 2026

Checked all 158 available storefront routes at 320px, 375px and 414px: 474 portrait route checks. This includes all 70 products, 42 categories and 17 articles, plus listings, guest account routes, order/tracking, support and empty/error screens. Checks waited for actual product/category/article content and checked both document overflow and text clipped by its own box.

Also checked representative screens at 667px and 844px in landscape, all six signed-in account screens at 320/375/414/667px, populated cart/checkout at the same four widths, and a long product title in quick view. The longest in-stock product name was used for cart/checkout. No order was submitted; the cart item was removed afterward. The isolated local account fixture was signed out, its tokens revoked, and soft-deleted again. Order count remained seven.

Fixed:

- Product-card, blog-card, cart and checkout titles now wrap fully below 1024px instead of using fixed line limits. Desktop truncation remains at 1024px and above.
- Brand names wrap beside a smaller mobile image, with a header that can grow naturally.
- Catalog, category and brand listing headings get the full mobile width, with the Filter control on the following row.
- Verified the 768px breakpoint no longer reintroduces clipping on larger phones in landscape.

The larger fonts were retained. Final saved checks found no clipped text or page-level horizontal overflow. The build and `git diff --check` passed; the TypeScript check introduced no new diagnostic signatures beyond the existing baseline.

Evidence: `artifacts/mobile-large-font-audit/results.json`, per-check history in `results.jsonl`, populated commerce measurements in `commerce.json`, and screenshots in the same directory.

Scope: campaign-detail and published CMS records are unavailable locally, so available empty/missing states were checked. Existing local test order 10006 supplied populated order/tracking coverage. Signed-in account checks used the isolated audit customer. Live payment, OTP and external communications were not exercised for this layout audit.
