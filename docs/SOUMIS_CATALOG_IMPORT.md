# Soumis Bangladesh catalog import

The public catalog from https://soumisbangladesh.com/ was collected on 16 October 2026 and imported into this project's local `shomiz_new` database.

## Result

- 70 live products and 70 variants.
- 42 categories with the original hierarchy, including six root categories.
- 238 downloaded product/category image files (about 31 MB).
- 50 in-stock and 20 out-of-stock products. The public variant endpoint provided exact quantities; no stock quantities were invented.
- Names, source SKUs where available, BDT prices, weight options, descriptions, ingredients, usage instructions, SEO metadata and galleries are imported.
- Two obsolete sitemap product URLs returned HTTP 404 and were excluded. The source shop lists all 70 imported products.
- No source-store accounts, carts or orders were created or modified. Source reads used public HTML, the sitemap and the public variant-price endpoint only.
- The local database contains zero orders.

The source manifest is `database/imports/soumis/catalog.json`. It records source URLs, source IDs, collected variant responses, category membership, media provenance, unavailable URLs and collection time.

## Open the actual application

- Storefront: http://127.0.0.1:8008/products
- Admin product manager: http://127.0.0.1:8008/admin/products
- Local admin credentials: `storage/app/local-admin-credentials.txt` (ignored by Git, mode 0600).

PHP dependencies are installed and the Laravel schema is migrated. The local `.env` uses a fresh application key, log-only mail and no payment-provider credentials. It does not reuse the source store's environment or credentials.

This project uses an isolated MySQL instance in `storage/local-mysql/`, reachable only through `/tmp/shomiz_new_mysql.sock`; it has no network listener and does not alter the pre-existing MySQL server. This is a local development setup, not a production database deployment.

To restart the local application on this Mac:

```bash
./scripts/start-local.sh
```

To stop only this project's isolated database after stopping PHP:

```bash
/opt/homebrew/opt/mysql/bin/mysqladmin --no-defaults --socket=/tmp/shomiz_new_mysql.sock -uroot shutdown
```

Database files, credentials, downloaded source-page cache and uploads are intentionally ignored by Git. An image/manifest import archive is saved at `storage/app/catalog-imports/soumis/soumis-catalog-import.tar.gz`; it contains no `.env`, admin credentials, sessions or customer data.

## Reproduce or update the import

Install the scraper's dependencies in a Python environment:

```bash
python3 -m pip install -r scripts/catalog/requirements.txt
python3 scripts/catalog/scrape_soumis.py
```

The scraper reads the current public source catalog and downloads images into `public/uploads/soumis/`. It verifies image files and writes a manifest only after collection. Inspect its `failures` array; the importer refuses incomplete manifests. `--reuse-products` reuses previously collected product details and should only be used to repair a cached collection, not to refresh live prices or stock.

Import into an already configured application with an existing shop:

```bash
php artisan catalog:import-soumis --shop=1
```

The importer uses one database transaction. It matches products/categories by source slug, media by local path, and variants by product/option code. Rerunning it updated 70 existing products and created zero duplicates. It does not delete unrelated products, import orders, or replace existing application settings. The optional `--bootstrap-local` mode is restricted to a new empty local database and was used to initialize this checkout.

Stock quantities represent the source snapshot and are not synchronized automatically. The source store did not expose purchase costs or other private inventory/accounting data; these were not fabricated.

## Verification

```bash
php scripts/catalog/verify_soumis.php
python3 scripts/catalog/verify_soumis_http.py
```

`database-verification.json` confirms all 70 products match their source names, descriptions, metadata, prices, stock quantities, category memberships and galleries. There are 70 variants, 42 categories, six root categories, 238 media files and zero orders.

`http-verification.json` confirms all five storefront API pages contain the complete 70-product catalog, every product detail endpoint returns its expected description, variants and gallery, and all 238 local image URLs return successful image responses. Both verification reports contain an empty `issues` array.

The real admin product manager and an imported product detail page were verified in the browser. Screenshots are under `artifacts/soumis-import/`.

`initial-import-report.json` records the initial creation of 70 products; `import-report.json` records the successful duplicate-free rerun.
