# Homepage content editor

Open **Website Setup → Home Page**, or `/admin/homepage/content`.

The editor uses the existing `homepage` permission and the normal admin authentication, CSRF and request middleware. Save changes, then refresh the storefront. Content is stored in the `botanicalHomeContent` system setting and delivered with the server-rendered storefront configuration; no rebuild is required for future editorial changes.

## Editable content

- Hero: visibility, kicker, title, script line, description, image, alt text, both button labels and destinations, delivery/payment badges, stamp text.
- Benefits strip: visibility and three titles/descriptions.
- Categories: visibility, heading, eyebrow, link, selected categories and display limit. Names, counts and images come from the category catalog.
- Bestsellers and new arrivals: visibility, headings, descriptions/links, automatic or manually selected products and display limit (1–24). The automatic queries retain published-shop, published-product and storefront visibility filters. Prices, images, availability and stock remain catalog-driven.
- Both promotional banners: visibility, headlines, supporting text, links, button labels; everyday banner image/alt and combo footer note.
- Arrivals offer card: visibility, title, kicker, description, button, destination and image/alt.
- Reviews: visibility, heading, eyebrow, link and display limit. Review entries, names, photos, ratings and text use the existing review editor linked at the top. Both editors share review visibility.
- Footer: service-strip visibility, account/explore headings and four editable links in each column. Footer configuration still manages contact information, description, service items, copyright, social links, payment image and additional CMS/quick links. Logo/branding and header navigation have direct links to their existing editors.
- Additional homepage sections continue to use the existing dynamic-section editor.

The old Theme One layout controls are replaced by a link to this editor to avoid advertising controls that the botanical layout does not render. Existing values are used on first load so this addition preserves the current design.

## Verification (2026-10-17)

- Signed in to the local admin and saved a temporary hero heading; confirmed it rendered on the homepage.
- Selected only Brightening Combo and Hair Growth Combo for bestsellers; confirmed only those two appeared.
- Changed the everyday banner heading; hid benefits and the arrivals side banner; confirmed all changes and that the arrivals grid released the empty column.
- Restored the approved copy, automatic products and visible sections.
- Uploaded the existing Bangladesh hero through the media manager, selected it, saved it and confirmed its new media URL loaded on the storefront. Its visual content is unchanged.
- Oversized uploads previously returned success with an empty result. The upload endpoint now reports validation/failure, and the picker retains its error/retry screen when nothing uploads. Current PHP limit is 2 MB, displayed beside image controls; the application maximum is 10 MB.
- Checked the homepage at 320, 375, 414, 768 and 1440 px: no page overflow or clipped content. Script-font descender overflow is visible, not clipped.
- Four targeted tests pass: unsafe link rejection, bounded product limits, guest authentication redirect and JSON upload validation errors. Production build passes. Existing 87 TypeScript diagnostics remain; no new diagnostics.

Evidence: `artifacts/dynamic-home/`. No order or payment was submitted during these checks.
