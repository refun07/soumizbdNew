# Steadfast live configuration

Configured through the authenticated live admin on 28 October 2026, Bangladesh time:

- Site: `https://soumisbangladesh.com/`
- Settings: `/admin/configurations/delivery-partner-settings`
- Provider base URL: `https://portal.packzy.com/api/v1`
- API key and Secret key saved exactly as supplied by the user; activation enabled.
- The admin Delivery Partners Overview reports Steadfast credentials configured.
- Credentials were not added to source files, screenshots, reports or this document.

The endpoint is listed in the [provider-authored SteadFast API documentation](https://wordpress.org/plugins/steadfast-api/). The supplied merchant API page requires a merchant login.

## Live authentication result

A private CLI check bootstrapped the deployed Laravel application and called the read-only `/get_balance` endpoint using its saved `services.steadfast` configuration. It returned HTTP **401 Unauthorized**, with provider status **401**. Authentication is not verified and shipment booking is not ready. The original service's `getBalance()` call also failed.

No shipment, pickup, customer payload or order status mutation was performed. No balance amount was retained. The private report contains configuration booleans, status codes and timestamps only. Temporary cron jobs were removed after both checks; cPanel confirms No Cron Jobs.

Evidence is in `artifacts/steadfast-setup-20261028/`. The live server reports are private under `/home/raisamar/shomiz-deploy-data/` and are not web endpoints.

## Corrected and authenticated

At `2026-10-28T11:12:03+00:00`, the authenticated merchant dashboard revealed that the originally supplied secret was truncated. The complete existing key was copied privately from the dashboard into the live site's Steadfast configuration. No key was generated or revoked.

The official authenticated API guide confirmed the existing base URL and headers. A fresh Laravel CLI bootstrap on the live server then called the application's actual `SteadfastService::getBalance()` method. It returned provider status **200**, with a valid numeric balance response. The report records only validity, not the balance amount. The persisted credentials match the dashboard, activation is enabled, and the authenticated live admin confirms Steadfast is configured.

Only the four Steadfast environment settings were updated. A private environment backup was retained on hosting, cached configuration was cleared, and the temporary runner removed its injected credentials before the API request. Its temporary cron was removed; cPanel confirms **No Cron Jobs**.

No shipment was booked, no pickup was requested, and no order was modified. Shipment creation remains untested against the real provider to avoid creating a real parcel.

Evidence: `artifacts/steadfast-setup-20261028/correction/live-result.json` and `live-authentication.png`. The local runner template contains no credentials.
