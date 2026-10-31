# Customer review section

Added a centered testimonial panel after new arrivals and before the service/footer area, following the supplied reference: round portraits, customer wording, names/roles, optional ratings, pagination and arrow controls. Three reviews show on desktop, two on tablet and one on mobile.

## Content and administration

Four existing testimonials were imported from the original Soumi's homepage saved during the catalog migration. Their wording and roles are preserved. The unrelated bicycle/Niceshop template entry was excluded. No ratings were invented: only the anonymous source entry explicitly containing five stars has a numeric rating; the other three render without stars. No verified-purchase badges or aggregate rating claims were added.

- Entries/photos/roles/ratings: `/admin/homepage/reviews`, also linked from **Website Setup → Home Page → Edit customer reviews and extra sections**.
- Heading/highlighted word, visibility and count: **Home Page → Customer reviews**.
- The dedicated entries page avoids the old missing `/admin/pages/configure/1` record.
- A name and review text are required to appear. Photos and ratings are optional. Empty rows are filtered. The editor keeps a spare row for adding entries without accumulating extra blank rows after saves.

Import manifest: `database/imports/soumis/reviews.json`. Local-only guarded importer: `scripts/catalog/import_soumis_reviews.php`. A pre-import settings backup is retained in the ignored `storage/app/catalog-imports/soumis/reviews-before-import.json`.

## Verification

- Admin changes were saved and appeared on the storefront; the temporary role edit was restored. Stored text, roles, names and ratings match the source manifest.
- Unit tests cover photo-less reviews, missing/invalid ratings and filtering incomplete rows.
- Desktop/mobile layout and image loading checked. Native carousel controls and pagination are keyboard accessible; the pagination hit area is separated from the arrow buttons.
- Production build passes; existing TypeScript baseline retained with no new diagnostics.

Evidence: `artifacts/customer-reviews/`.
