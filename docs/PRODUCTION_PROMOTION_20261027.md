# Soumis production promotion — 27 October 2026

The user authorized replacing https://soumisbangladesh.com with the running test site, using the test site's data and preserving the old production database separately. The user also explicitly approved full permissions for existing user `raisamar_test` on the new database `raisamar_soumis_live` only.

## Active deployment

- Public document root: `/home/raisamar/soumisbangladesh.com` (public files only).
- Private Laravel application: `/home/raisamar/soumis-live-app`.
- Production database: `raisamar_soumis_live`, copied from `raisamar_test`.
- Production runtime: PHP 8.2 (`ea-php82`). The old PHP 7.4 compatibility recovery is superseded.
- Staging application, database and public root remain separate and unchanged.
- Production environment uses the live URL, production indexing, debug disabled, and a separate session cookie/cache prefix.
- The host disables `proc_open`, `exec` and `symlink`. No server protections were disabled. Snapshots and import used PDO and gzip; the public storage disk uses an ordinary public directory.
- Preparation verified matching counts: 70 products, 3 users, 3 orders, 3 order groups and 101 system settings. These are test-site records, not migrated old-production history.

## Preserved rollback material

Private folder: `/home/raisamar/backupfolder/soumis-promotion-20261027`

- `old-document-root/`: the complete former live application and its original environment/media, moved intact.
- `old-production.sql.gz`: 991.62 KB compressed snapshot, mode 0600.
- `test-snapshot.sql.gz`: 257.82 KB compressed snapshot, mode 0600.
- The old live database itself was not overwritten or deleted.

Runner: `/home/raisamar/soumis-promote-20261027.php`, CLI-only and outside all web roots. Source is in `artifacts/production-promotion-20261027/promote.php`. Its temporary execution cron was removed after activation. Private release log and prepared/activated markers use prefix `/home/raisamar/soumis-promotion-20261027`.

If rollback becomes necessary, preserve the current new production directory/database first; move the old document root back and restore its PHP 7.4 runtime. The old and new database structures differ. Do not import one over the other. New orders after launch belong to the new database and must remain preserved.

## Live verification

- Desktop and 375px mobile homepage, real catalog and images loaded.
- Homepage canonical points to the live HTTPS domain; robots is `index, follow, max-image-preview:large`.
- Loaded homepage had zero staging links/image sources and no horizontal overflow.
- Catalog, product details and direct reload of Health Care category worked (2 products).
- Added one selected 25ml eye-gel verification item, checked cart subtotal ৳500, selected inside-Dhaka delivery ৳70, and checked checkout total ৳570.
- Removed the verification item; cart returned to empty.
- Admin URL resolves to the new dashboard login page.
- No real order, payment, or authenticated admin save was executed.

Screenshots and the machine-readable result are in `artifacts/production-promotion-20261027/`.
