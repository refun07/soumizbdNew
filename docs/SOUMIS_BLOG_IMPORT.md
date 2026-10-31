# Soumis Bangladesh blog import

Imported all 17 public articles discovered across `/blog`, its pagination, and the source sitemap on 2026-10-16. The local database contains 17 published blogs, 6 blog categories, and 56 locally stored images (banners, thumbnails, metadata and inline content). Article bodies retain their English and Bengali text, headings, lists, images and links; known source product, category and blog links point to local routes.

The public source exposes no article publication dates or authors. Import dates are not used as publication dates, and the storefront hides missing dates/authors. Categories are Hair Care, skin care, Oily Skin Care, Organic Skin Product, Hana Beauty, and sensitive skin.

## Local pages

- Storefront: http://127.0.0.1:8008/blogs
- Admin: http://127.0.0.1:8008/admin/blogs
- Categories: http://127.0.0.1:8008/admin/blog-categories

This import is local; it has not been deployed to external hosting. The existing 70 imported products remain intact. No orders were created.

## Reproduce

Use the Python dependencies in `scripts/catalog/requirements.txt` and the existing local Laravel environment:

```sh
python3 scripts/catalog/scrape_soumis_blogs.py
php artisan catalog:import-soumis-blogs
php scripts/catalog/verify_soumis_blogs.php
python3 scripts/catalog/verify_soumis_blogs_http.py
```

The scraper reads public source pages and their referenced images. Only the source domain and its observed Squarespace image CDN are allowed for downloads. It sanitizes article HTML and checks normalized text hashes after localization. An incomplete collection cannot be imported. Re-import updates by slug without creating duplicates; it does not remove unrelated blogs.

The manifest and reports live in `database/imports/soumis-blogs/`. Images are in `public/uploads/soumis-blogs/`, which is ignored by Git. A portable archive containing the manifest, media, import command and documentation is saved to `storage/app/catalog-imports/soumis-blogs/soumis-blogs-import.tar.gz`. It excludes environment files and credentials.

## Verification

- All 17 database records, categories, translations, metadata and image references matched the manifest.
- All 17 detail APIs and both API listing pages passed; all 56 image URLs returned HTTP 200 with image content types.
- All 17 normalized article text hashes matched the collected source text.
- A repeated import created 0 records and updated 17.
- Rendered storefront, Bengali article content, inline images, category filtering and admin listing were checked in the browser. Screenshots: `artifacts/soumis-blogs-import/`.
- Production build passed. TypeScript retains the existing 87 errors with no additional errors from these changes.
