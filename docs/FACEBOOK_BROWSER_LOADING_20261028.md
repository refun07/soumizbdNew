# Facebook in-app browser startup reliability

Deployed to https://soumisbangladesh.com/ on 28 October 2026 (Bangladesh time).

The reported hang was inside Facebook's app browser. Regular Chrome and emulated Facebook user agents loaded successfully before the fix, so the exact device-specific trigger could not be reproduced here. Inspection found real startup failure paths: top-level unguarded local/session storage access and unguarded checkout JSON parsing. Any exception there prevented React from replacing the initial branded loader.

Changes:
- Use guarded storage for storefront storage reads/writes, with temporary per-page memory storage when persistence is denied. Normal persistent storage behavior is retained.
- Discard malformed saved checkout JSON, merge valid customer fields with defaults, and validate the basic saved container shapes.
- Independently expose a retry control after 12 seconds if the initial app loader still exists. Retry retains the complete requested URL, including Facebook attribution. No automatic reload loop is introduced.
- Keep the existing guarded chunk-reload behavior; it intentionally avoids auto-reloading when session storage is unavailable.

Verification:
- 16 focused Node tests passed, including storage getter/access/write failures, malformed checkout JSON and state shapes, valid checkout retention, retry fallback, successful mount and chunk recovery.
- Production build and deployment PHP syntax checks passed. Built against a downloaded snapshot of deployed resources; source checksum guards passed before publication.
- Live apex and www URLs serve the new entry `index-8e0c721a.js`.
- Android and iPhone Facebook browser user-agent profiles at 390 x 844 loaded the homepage and product/category data. This is Chrome emulation, not a real Facebook app/WebKit test.
- Blocking the live entry script in the agent's own tab displayed the retry screen. Unblocking and clicking Try again restored the same www URL and fbclid query.
- Health Care category rendered three products. Existing cart and checkout rendered at 390px without horizontal overflow. No cart quantities were changed and no order was submitted.
- Temporary CDP overrides were reset. Temporary deployment cron job was removed; cPanel shows No Cron Jobs.

Production rollback backup: `/home/raisamar/shomiz-deploy-data/fb-release-20261028/before-update.zip`. Old hashed assets remain available to previously open tabs. No environment settings, database records, credentials, permissions or delivery rates were changed.

Evidence: `artifacts/facebook-loader-20261028/` contains the release specification, deployment result and screenshots. Actual Facebook app verification still requires the user to reopen the link in their app.

## Follow-up: 28 October, 01:28 Bangladesh time

The user reported that the logo and spinner still remained inside the actual Facebook app after the first release, including a fresh diagnostic URL. The first release therefore did not establish that the reported device issue was resolved.

Published release `20261028-startup-2` changes only the frontend Blade template and its response cache headers:
- Start the recovery timer and error listener at the beginning of the document head, before external stylesheets.
- Load Google Fonts without blocking the document parser.
- Add a CSS-only delayed retry control, independent of JavaScript execution.
- Preserve the requested URL when retrying and add a release-specific cache key.
- Send no-store response headers to reduce stale document reuse.
- On the explicitly requested `?loading-check=3` URL only, request technical startup-stage marker images from the same site. Normal shoppers send no diagnostic markers. Error markers contain an asset basename and line number, not exception messages or URL query strings.

Verification: 18 focused Node tests passed. PHP syntax checks passed. On the live site, blocking the entry bundle displayed the retry screen, and clicking it after unblocking restored the homepage. Local browser checks also verified recovery with a deliberately stalled font stylesheet and with JavaScript disabled. These tests do not replace actual Facebook app verification.

The private deployment runner completed at `2026-10-27T19:28:01+00:00`; the temporary cron job was removed and cPanel shows No Cron Jobs. No database records, environment settings, delivery rates or permissions changed. The existing entry remains `index-8e0c721a.js`.

Rollback backup: `/home/raisamar/shomiz-deploy-data/fb-followup-20261028/before-update.zip`. Local evidence: `artifacts/facebook-loader-followup-20261028/`.

The user subsequently confirmed that the actual Facebook browser still showed only the logo and spinner for the diagnostic link.

## HTTP origin correction

A fresh actual Facebook visit at 01:36 Bangladesh time requested HTTPS module assets with the document referrer `http://soumisbangladesh.com/`. Direct public HTTP checks confirmed that HTTP served a 200 storefront document containing absolute HTTPS module URLs, while the module responses supplied no cross-origin allow header. This is a concrete startup failure path for HTTP visits.

The production public `.htaccess` was backed up and updated to redirect apex HTTP and www traffic to the HTTPS apex with 308 redirects. The rule preserves paths and query parameters and exempts ACME challenge paths. Existing PHP handlers and front-controller rules were retained. Live checks verified HTTP home, HTTP www, HTTPS www, and a category URL with query parameters redirect as intended; HTTPS apex returns 200. The rendered mobile homepage mounted and showed 14 Add to cart controls after the redirect. No order was submitted.

Original and updated production rules are saved in `artifacts/facebook-loader-followup-20261028/production-htaccess-before.txt` and `production-htaccess-after.txt`. A private hosting copy was requested at `/home/raisamar/shomiz-deploy-data/fb-followup-20261028/htaccess-before-https`; it was not subsequently reopened to verify its contents.

Status: the user still reported the loader after this redirect change. Fresh logs were being checked when the user switched to Steadfast configuration. Actual Facebook startup remains unresolved; do not claim the HTTP correction proves the phone issue is fixed.
