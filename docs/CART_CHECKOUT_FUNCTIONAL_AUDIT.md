# Cart and checkout functional audit

Date: 2026-10-17. Local storefront at `http://127.0.0.1:8008/`.

## Verified in the browser

| Check | Result |
| --- | --- |
| Add two different products as a guest | Pass: ৳500 + ৳650 = ৳1,150 |
| Cart persistence after reload | Pass: both products retained |
| Product purchase limits | Pass: the tested products allow one per order; increase controls are disabled at that limit |
| Inside-Dhaka quote | Pass: ৳1,150 + ৳70 = ৳1,220 |
| Outside-Dhaka quote | Pass: ৳1,150 + ৳150 = ৳1,300 |
| Remove from checkout | Pass: remaining subtotal ৳500; inside-Dhaka total ৳570 |
| Remove from cart | Pass: empty state displayed and checkout protected |
| Empty name and invalid Bangladesh phone | Pass: validation blocks submission |
| Whitespace-only name/address | Pass: blocked; invalid field receives focus |
| Payment selection | Cash on delivery selected and exposed with `aria-pressed` |
| Final request construction | Pass: two cart IDs, selected delivery zone and cash-on-delivery method; trimmed name/address |
| Duplicate-submit protection | Submit disabled during the intercepted request; synchronous guard also prevents another request |
| Delivery API failure | Simulated HTTP 503: error and retry displayed, order submission blocked until a successful quote |
| Submission network failure | Request aborted before the server: checkout retained details/cart, displayed an error and re-enabled retry |
| Guest coupon entry | Sign-in prompt displayed; no discount applied without authentication |
| Mobile controls | Previous mobile audit covered 320px, 375px and 844px landscape; fields use 16px text and touch controls at least 44px |

## Repairs

- Whitespace validation, normalized submitted values, field focus, input labels/autofill, payment selection semantics.
- Guarded duplicate submissions, empty selections and unavailable delivery quotes.
- Failed delivery quotes can be retried; quote selection is derived from current query results rather than stale callbacks.
- Failed order requests keep customers on checkout with their details and an actionable error.
- Published delivery information and its admin/default values now match the confirmed rates: inside Dhaka ৳70, outside Dhaka ৳150.
- Empty checkout redirects to the empty cart without competing redirects.

## Safety and limits

Order API requests were intercepted and aborted before reaching the server. Database counts remained **3 orders / 3 order groups** before and after testing. The two temporary cart items, synthetic customer details and coupon input were cleared. Browser request interception was cleared.

A real order, live payment, production SMS/OTP flow, and authenticated valid-coupon redemption were not executed. The test covered guest checkout using the currently configured cash-on-delivery method.

Production build and PHP syntax checks pass. TypeScript retains the 87 existing baseline errors; no new error signatures were present in the functional-check typecheck. The previous optional Swiper breakpoint typing issue was also corrected.

The previous local footer setting is backed up at `storage/app/delivery-info-before-70.json`.
