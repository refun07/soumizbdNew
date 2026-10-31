# Mobile bottom navigation

Added the existing Raisamart-style `BottomBar` to the botanical storefront's shared layout. It provides Home, All Products, Brands, My Cart with a live quantity badge, and Login (Account for signed-in customers). Links close popup state and return the page to the top. Cart and authentication use the existing storefront state and popups.

The bar uses Soumi’s green styling, five equal columns, visible keyboard focus, active-route markers, accessible button names and safe-area padding. It appears below 1024px. The fixed side cart remains desktop-only; the mobile bottom cart is available on every storefront page. Floating chat and product purchase controls sit above the bar. The wrapper reserves its exact height so footer content stays visible.

Verified locally on 2026-10-17:

- 320, 375, 414 and 768px: 72px navigation, no cut labels or horizontal overflow.
- 1024 and 1440px: navigation hidden; no reserved mobile spacing.
- Home, All Products and Brands navigate correctly and active indicators update.
- Adding one product updates the cart badge. My Cart opens the populated drawer. The test item was removed and the cart restored to empty.
- Login opens the existing sign-in dialog; no authentication or order was submitted during this check.
- At 375px the product purchase bar's bottom and mobile navigation top both equal 740px: no overlap.
- At 320px the footer copyright ends above the navigation at the end of the page.
- Production build and diff whitespace checks passed; no new TypeScript diagnostics against the existing 87-error baseline.

Screenshots and measured results: `artifacts/mobile-bottom-menu/`.
