# Homepage flash sale

The homepage now includes a red flash-sale product carousel after featured categories and before bestsellers, following the supplied Raisamart reference. It reuses the shared botanical product cards and supports five cards on desktop, swipe/pagination, native arrow buttons, and the existing cart/quick-view/wishlist behavior.

Edit **Website Setup → Home Page → Flash sale** (`/admin/homepage/content`):

- Show/hide, heading, eyebrow, description, button label/destination and background color.
- Select products and display count (1–24). Empty selection uses existing on-sale selections, then published catalog picks ranked by sales. Published-shop and storefront visibility filters remain in place.
- Optional sale deadline in Dhaka time (UTC+6). The countdown appears only with a configured date; it shows days/hours/minutes/seconds and hides the sale when the deadline is reached. Blank deadline means no countdown.

The new homepage endpoint and `/on-sale-products` use the same selection. Disabling the homepage section leaves the collection accessible. Expired selections return no sale products. Catalog pricing and discounts are not modified. No discounts, campaign or deadline existed at implementation time; the default section therefore uses eight existing products at their current prices, with no invented timer or discount.

## Verification

- Admin selection of two products appeared in the homepage section and exactly matched View more results.
- A future admin deadline produced the correct Dhaka-time countdown; a past deadline hid the section. Restored blank deadline and automatic products afterward.
- Native next/previous buttons advance and return the slider. A pointer-navigation issue in the shared Swiper-generated controls was fixed by using native React buttons tied to each slider instance.
- Add to cart added a sale item at its catalog price; the test item was removed and the cart restored to empty. No order was submitted.
- Checked 320, 375, 414, 768 and 1440px: no page overflow or clipped sale heading/link. Product card text uses the existing mobile wrapping behavior.
- Three countdown unit tests and five homepage admin/validation tests pass. Build and whitespace checks pass; no new TypeScript diagnostic signatures against the existing baseline.

Evidence: `artifacts/flash-sale/`.
