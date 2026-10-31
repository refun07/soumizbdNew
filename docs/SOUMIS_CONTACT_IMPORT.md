# Soumis Bangladesh contact details

Verified against the public homepage and Contact Us page on 2026-10-16:

- Phone: +880 1755-732210 (click-to-call +8801755732210)
- Email: support@soumisbangladesh.com
- Full address: Shop # 01, Level # 04, Green Swaranika Shopping Mall, 369 Elephant Road, Dhaka-1205, Bangladesh

The source footer gives the shorter address, Elephant Road, Dhaka-1205, Bangladesh. Its homepage Organization structured data provides the shop, floor, mall and street number used here.

These are saved in the existing `homeFooterCustomerSupport`, `homeFooterEmail`, `homeFooterAddress` and `topbar_hotline` settings, editable through the existing website/footer configuration. The storefront footer and initial HTML display them. Homepage Organization schema uses the same saved contact values. Telephone links contain the normalized international number.

The manifest and before/after import report are in `database/imports/soumis-contact/`. Reapply with `php scripts/catalog/import_soumis_contact.php`. This updates only these four settings and clears the settings cache. The change is local and has not been deployed externally.

Sources: https://soumisbangladesh.com/ and https://soumisbangladesh.com/contact-us

## Original logo

The exact PNG from `https://soumisbangladesh.com/public/assets/img/logo.png` is saved unchanged at `public/images/storefront/soumis-logo.png` (492 × 314 pixels, SHA-256 `4c1f1581793748ae6cfc13f01b97162b22891b97458e5fa6ebd2c6962c5f3062`). The local header/footer branding settings and isolated preview use this asset. Colors, lettering, tagline and transparency are preserved; only CSS display size changes. Reapply the settings with `php scripts/catalog/import_soumis_logo.php`. The import report is `database/imports/soumis-contact/logo-import-report.json`.
