# Storefront SEO verification

Implemented and verified locally on 2026-10-16. The imported SEO titles/descriptions remain editable through the existing product, category, blog and website settings screens.

## Implemented

- One server-rendered title, description, canonical link and set of Open Graph/Twitter tags per page. Category/brand canonical URLs no longer incorrectly point to product URLs.
- Imported SEO titles and descriptions are used, with readable fallback text for missing fields. All 17 article social images use the source page's article-specific image instead of its duplicate generic logo tag.
- Metadata updates on React navigation as well as direct page loads, including removing stale structured data when leaving a detail page.
- Initial HTML includes the title, article/product content and crawlable links before JavaScript mounts. JavaScript-enabled visitors see the branded loading screen until React mounts; without JavaScript, the server-rendered fallback remains visible. The same HTML is sent to people and crawlers; there is no user-agent-specific output.
- Product JSON-LD uses actual variant prices, BDT currency, stock availability and brand. BlogPosting and breadcrumb JSON-LD describe articles without inventing author names, publication dates or ratings.
- Dynamic `/sitemap.xml` includes 135 current canonical URLs, including all 70 products, 42 categories and 17 blogs. Hidden/unpublished products and blogs are excluded. It is generated from the database, so publishing changes are reflected immediately.
- `/robots.txt` advertises the sitemap. Production allows public crawling and excludes internal paths. Local/staging copies are blocked from indexing. Cart, checkout, account and filtered search pages also carry `noindex` metadata.
- Missing and unpublished records return HTTP 404. Source `/product/{slug}`, `/category/{slug}`, `/blog/{slug}`, `/shop` and `/blog` URLs permanently redirect to their new routes. Old blog listing pagination is translated from 12 to 20 posts per page.
- Product/category listing titles use an H1. Pagination has actual anchor destinations and page-specific canonical links. Tracking parameters are omitted from canonical URLs.

## Confirmed checks

- `artifacts/seo/http-audit.json`: all 129 imported detail pages, their old URL redirects, initial HTML, titles, canonical/social tags, product offer data, article text and article schema passed. Sitemap contains every imported page; 404, filtered/private pages and pagination cases passed.
- `artifacts/seo/visibility-audit.json`: production indexing allowed, local indexing blocked, unpublished article/hidden product excluded from detail pages and sitemap. Temporary visibility changes were rolled back.
- `artifacts/seo/browser-audit.json`: metadata and schema changed correctly during actual browser navigation from journal to article to catalog to product; catalog pagination links and H1 checked after the build.
- Production frontend build passed. TypeScript has the existing 87 baseline errors and no new errors from this change.

Repeat the checks:

```sh
php scripts/catalog/verify_seo_visibility.php
python3 scripts/catalog/verify_seo.py
```

The Python check uses the dependencies in `scripts/catalog/requirements.txt` and the local server on port 8008.

## Public deployment

Set `APP_URL` to the final HTTPS domain and `SEO_INDEXABLE=true` only on the public site, then clear Laravel's configuration cache and rebuild/cache configuration for that environment. Default indexing is enabled for `APP_ENV=production` and disabled otherwise when the explicit override is absent. Keep staging `SEO_INDEXABLE=false`.

On the final public host, verify robots allows crawling, canonical/social/sitemap URLs use that domain, legacy links redirect correctly, and all assets load. Submit `/sitemap.xml` in Google Search Console and inspect representative product/blog URLs. Rich Results Test and URL Inspection require a publicly reachable deployment; they have not been completed against this localhost copy. Ranking and retention of the old site's search positions cannot be verified locally.

If moving domains, old-domain redirects must also be configured on the old host. These application redirects cover old paths when requests reach this application.

References: [Google JavaScript SEO](https://developers.google.com/search/docs/crawling-indexing/javascript/javascript-seo-basics), [canonical URLs](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls), [ecommerce URL structure](https://developers.google.com/search/docs/specialty/ecommerce/designing-a-url-structure-for-ecommerce-sites).
