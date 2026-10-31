# Global website title

The live website's global name and default title were set to:

`THE SOUMI'S CAN PRODUCT BANGLADESH`

Only `systemName`, `systemTitle`, `meta_title`, and the matching `APP_NAME` environment setting were updated. The previous values and environment file were backed up privately under `/home/raisamar/shomiz-deploy-data/site-title-20261028/`; permissions on the backup are 0600. Other settings and orders were not changed.

Verified on the live site after configuration cache clearing:

- Homepage title and `og:site_name`: the exact requested name.
- Catalog: `All Products | THE SOUMI'S CAN PRODUCT BANGLADESH`.
- Cart: `Cart | THE SOUMI'S CAN PRODUCT BANGLADESH`.
- Admin: `General Settings | THE SOUMI'S CAN PRODUCT BANGLADESH`.
- System Name and System Title fields display the exact requested name.

Individual products retain their separately configured custom SEO titles; no product SEO records were rewritten. The temporary private execution job was removed. Local deployment evidence and the saved settings screenshot are under `artifacts/site-title-20261028/`.
