# Botanical storefront redesign

The customer-facing design follows the supplied skincare reference: mint and forest green, a photographic hero with script lettering, round categories, round featured product photography, a promotional banner, an arrivals grid, testimonials, newsletter and footer. The existing Laravel admin code, database schema, payment integrations, order processing and authentication endpoints are unchanged.

## Local design preview

Run `npm run dev:storefront`, then open http://127.0.0.1:5178/preview/storefront/.

This is an isolated frontend preview with an explicitly labelled sample catalog, sample reviews and simulated API responses. It renders the production layout, homepage, product cards and existing cart/product-preview components. It cannot create a real order or subscribe an address to an external mailing list. Account and checkout routes show a preview notice. It does not load the production entry point, and the preview files are not included in the production build.

Preview states:

- `?previewState=empty`: empty product sections.
- `?previewState=catalog-error`: collection failures with retry buttons.
- `?previewState=subscribe-error`: newsletter failure feedback.

The actual Laravel application is now initialized locally with the Soumis catalog. Its product storefront, API and admin product manager have been verified. Payment processing and unrelated admin workflows remain untested. See [SOUMIS_CATALOG_IMPORT.md](SOUMIS_CATALOG_IMPORT.md) for the local application and import details.

## Existing admin content

- Logo and store name: existing general settings.
- Navigation: existing navbar custom links, when enabled; reference-style links otherwise.
- Hero: Theme One main-banner settings, including title, subtitle, image, link and section visibility. The first configured banner is used for the reference's static hero.
- Categories: Theme One featured categories, images, counts and section visibility.
- Featured products: existing best-sellers endpoint, real prices and stock.
- Promotion: Theme One single-image-banner settings and visibility.
- Arrivals: existing new-arrivals endpoint.
- Reviews: Theme One review settings and visibility. No sample testimonials are added to production.
- Custom slider sections: existing dynamic section renderer.
- Footer: existing description, contact information, default pages, quick links, service descriptions, social links, payment image and copyright settings.
- Newsletter: existing subscription mutation, with explicit success/failure feedback.

The new layout replaces the old storefront theme/header/product-card/footer presentation. The old theme components remain in the source. Their previous layout choices, colors and ancillary homepage blocks are not rendered by this design.

## Verification

- Production Vite build passed, with all 32 configured entry points present in the manifest.
- Full TypeScript comparison: 87 current diagnostics versus 90 in the unchanged clone; no new diagnostics from this redesign. Existing unrelated type errors remain.
- Browser preview checked at desktop 1440×1000, mobile 375×812, and landscape 812×375 with reduced motion enabled.
- Checked no horizontal page overflow, product touch targets, mobile menu, anchor navigation, search results, add-to-cart and cart badge/sidebar, product quick view, and newsletter success/failure feedback.
- Collection loading, failure/retry, and empty states checked against simulated API responses.
- Screenshots are in `artifacts/storefront-preview/`.

## Artwork

Built-in image generation was used for `public/images/storefront/botanical-hero.webp`. `public/images/storefront/botanical-detail.webp` is an optimized detail crop of that artwork. Both are project-local; there is no generated-image runtime dependency.

Final generation prompt:

> Use case: ads-marketing. Asset type: photographic botanical skincare ecommerce hero artwork, landscape 3:2. Create an elegant photorealistic cosmetic campaign composition: a smiling adult woman in her late 20s, long natural light brown hair, healthy glowing skin, bare shoulders wearing a white strapless top. Woman on the right two thirds from chest upward, looking at camera. Large vibrant green palm leaves behind her and a small amber glass skincare dropper bottle in foreground lower left. Pale almost-white mint studio backdrop (#e8f3ec), soft diffused natural sunlight, clean luxury beauty editorial photography, realistic skin texture. Background should seamlessly approach solid pale mint at outer edges. No text, no letters, no logos, no frames, no badges, no watermark. The attached website screenshot is only context for mood, make a standalone photo asset.

Isolated preview photography was downloaded from Unsplash images 1608571423902-eed4a5ad8108, 1620916566398-39f1143ab7be, 1556228720-195a672e8a03, 1571781926291-c477ebfd024b, 1596462502278-27bfdc403348, 1612817288484-6f916006741a and 1608248543803-ba4f8c70ae0b. These sample images are only referenced by the preview catalog. Production category and product images come from the existing API.
