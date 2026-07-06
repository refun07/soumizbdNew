# PRD: Copy Existing GTM + Meta Pixel Tracking Flow

## Goal

Copy the current project's GTM, Meta Pixel, and Meta Conversions API tracking behavior into another Laravel + React storefront project with no new feature changes.

The target implementation must match the current flow as-is:

- Admin can configure GTM, GA4, Meta Pixel, Meta CAPI, ad account, test event code, and activation from the Analytics settings page.
- Frontend loads Facebook Pixel and Google Tag Manager.
- React pushes ecommerce events into `window.dataLayer`.
- Backend sends matching Meta Conversions API events with the same `eventId` when available, so Meta can deduplicate browser/server events.
- Existing event names, payload shapes, and product ID conventions must be preserved.

## Source Project Files To Copy Or Recreate

Use these files as the source of truth:

- `config/app.php`
- `config/services.php`
- `resources/views/backend/admin/configurations/analytics/index.blade.php`
- `app/Http/Controllers/Backend/Admin/Configurations/SettingController.php`
- `routes/admin.php`
- `resources/views/frontend/home.blade.php`
- `app/Http/Controllers/Frontend/HomeController.php`
- `resources/frontend/App.tsx`
- `resources/frontend/utils/gtmCustomerData.ts`
- `resources/frontend/hooks/useGTMEventNew.ts`
- `resources/frontend/hooks/useFacebookPixelEvents.ts`
- `resources/frontend/utils/fbPixel.ts`
- `resources/frontend/components/card/product-card/index.tsx`
- `resources/frontend/components/popups/product/ProductPreviewContent.tsx`
- `resources/frontend/pages/order/Checkout.tsx`
- `resources/frontend/store/features/checkout/checkoutApi.ts`
- `app/Services/FacebookConversionService.php`
- `app/Http/Controllers/Frontend/Api/TrackingController.php`
- `app/Http/Controllers/Frontend/Api/CartController.php`
- `app/Http/Controllers/Frontend/Api/WishlistController.php`
- `app/Http/Controllers/Frontend/Api/SearchController.php`
- `app/Http/Controllers/Frontend/Api/CheckoutController.php`
- `app/Http/Resources/ProductResource.php`
- `routes/api.php`
- `composer.json`

## Dependencies

Backend requires:

```json
"facebook/php-business-sdk": "^22.0"
```

If missing in the target project, install with Composer and keep the service API usage from `FacebookConversionService.php`.

## Environment And Config Keys

Add these env-backed config values in `config/app.php`:

```php
'GOOGLE_ANALYTICS_ACTIVATION' => env('GOOGLE_ANALYTICS_ACTIVATION', '0'),
'FACEBOOK_PIXEL_ACTIVATION' => env('FACEBOOK_PIXEL_ACTIVATION', '0'),
'META_PIXEL_ID' => env('META_PIXEL_ID', ''),
'META_CONVERSIONS_API_ACCESS_TOKEN' => env('META_CONVERSIONS_API_ACCESS_TOKEN', ''),
'FACEBOOK_AD_ACCOUNT_ID' => env('FACEBOOK_AD_ACCOUNT_ID', ''),
'FB_TEST_EVENT_CODE' => env('FB_TEST_EVENT_CODE', ''),
'GTM_ID' => env('GTM_ID', ''),
'GA4_API_SECRET' => env('GA4_API_SECRET', ''),
'GA4_MEASUREMENT_ID' => env('GA4_MEASUREMENT_ID', ''),
```

Add this in `config/services.php`:

```php
'gtm_id' => env('GTM_ID', null),
'ga4' => [
    'measurement_id' => env('GA4_MEASUREMENT_ID'),
    'api_secret' => env('GA4_API_SECRET'),
],
```

## Admin Settings Page

Route:

```php
Route::get('/analytics', [SettingController::class, 'analytics'])->name('admin.analytics.index');
```

Controller method:

```php
public function analytics()
{
    return view('backend.admin.configurations.analytics.index');
}
```

The Blade page must submit to the existing env update route:

```php
route('admin.env-key.update')
```

Fields to include:

- `GTM_ID`
- `GA4_API_SECRET`
- `GA4_MEASUREMENT_ID`
- `GOOGLE_ANALYTICS_ACTIVATION`
- `META_PIXEL_ID`
- `META_CONVERSIONS_API_ACCESS_TOKEN`
- `FACEBOOK_AD_ACCOUNT_ID`
- `FB_TEST_EVENT_CODE`
- `FACEBOOK_PIXEL_ACTIVATION`

Preserve the two-card layout: Google Analytics Credential and Meta Pixel Credential.

## Frontend Boot Scripts

In the frontend root Blade, currently `resources/views/frontend/home.blade.php`:

1. Expose Laravel settings:

```html
<script>
    window.config = @json($settings);
</script>
```

2. Load Facebook Pixel unconditionally, then initialize with:

```js
fbq('init', `${window.config.meta_pixel_id}`);
```

Important: this is the current behavior. Do not add a new activation guard unless specifically asked.

3. Load GTM only when `config('services.gtm_id')` exists:

```php
@if (config('services.gtm_id'))
```

The GTM head script must load:

```text
https://www.googletagmanager.com/gtm.js?id={{ config('services.gtm_id') }}
```

The GTM body noscript iframe must load:

```text
https://www.googletagmanager.com/ns.html?id={{ config('services.gtm_id') }}
```

4. Push initial GTM page metadata:

```js
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
    user_id: '{{ auth()->id() }}',
    page_type: '{{ Route::currentRouteName() }}'
});
```

## Settings Sent To React

In `HomeController`, include:

```php
'meta_pixel_id' => env('META_PIXEL_ID'),
```

The React app expects:

- `window.config.meta_pixel_id`
- `window.config.currency.code`
- `window.config.generalSettings.productInfiniteScroll`
- `window.config.generalSettings.allowBackorder`

## GTM Data Layer Utility

Copy `resources/frontend/hooks/useGTMEventNew.ts`.

Behavior:

- Exposes `useGTMEventsNew()`.
- Pushes event objects to `window.dataLayer`.
- Dedupes by event name + `event_id` or `eventID` or `eventId` or `meta_event_id`.

Supported event names:

- `page_view`
- `view_item`
- `view_item_list`
- `add_to_cart`
- `remove_from_cart`
- `add_to_wishlist`
- `remove_from_wishlist`
- `begin_checkout`
- `add_payment_info`
- `purchase`
- `search`

Copy `resources/frontend/utils/gtmCustomerData.ts`.

It provides:

- `sha256Hash`
- `createMetaEventId`
- `buildGTMCustomerContext`
- `mapCheckoutAddressToGTMAddress`
- `buildGTMUserData`
- `buildGTM4WPData`
- `buildGTM4WPCartContent`
- `buildGTM4WPOrderData`

Do not change the ID reuse behavior:

```ts
const EVENT_ID_REUSE_WINDOW_MS = 2000;
```

## React Page View Flow

In `resources/frontend/App.tsx`, preserve:

- Track route changes by `location.pathname + location.search`.
- Create event ID with `createMetaEventId('page_view', pageViewKey)`.
- Patch `fbq` so browser `PageView` receives the same `eventID`.
- Push GTM event:

```ts
{
    event: 'page_view',
    event_id: eventId,
    eventID: eventId,
    eventId,
    meta_event_id: eventId,
    page_location: window.location.href,
    page_referrer: document.referrer || '',
    ...buildGTM4WPData(...),
    customer: buildGTMCustomerContext(...)
}
```

- POST server CAPI event to:

```text
POST /tracking/page-view
```

Body:

```json
{
  "eventId": "string",
  "url": "current URL",
  "referrer": "document.referrer"
}
```

## Meta Conversions API Service

Copy `app/Services/FacebookConversionService.php`.

Constructor reads:

- `META_PIXEL_ID`
- `META_CONVERSIONS_API_ACCESS_TOKEN`

`sendEvent(eventName, userInfo, customData, eventId)` must:

- Return early when `config('app.FACEBOOK_PIXEL_ACTIVATION') == '0'`.
- Read `_fbc` and `_fbp` cookies.
- Set user data: email, phone, client IP, user agent, fbp, fbc.
- Support custom data: `currency`, `value`, `content_ids`, `contents`, `content_type`.
- Use `ActionSource::WEBSITE`.
- Set `event_source_url` from `customData['event_source_url']` or current URL.
- Set `eventId` for dedupe.
- Apply `FB_TEST_EVENT_CODE` when configured.

## Backend Routes

Add:

```php
Route::post('tracking/page-view', [TrackingController::class, 'pageView']);
Route::post('/checkout/initiate', [CheckoutController::class, 'initiateCheckout']);
```

Current source locations:

- `routes/api.php` page-view route near tracking section.
- `routes/api.php` checkout initiate route near checkout routes.

## Backend Server Events

### PageView

File: `app/Http/Controllers/Frontend/Api/TrackingController.php`

Validation:

- `eventId`: required string max 255
- `url`: required URL max 2048
- `referrer`: nullable string max 2048

If activation is off, return success with skipped message.

If activation is on, send:

```php
$this->facebook->sendEvent('PageView', [
    'email' => $user?->email,
    'phone' => $user?->phone,
], [
    'event_source_url' => $data['url'],
    'referrer' => $data['referrer'] ?? null,
], $data['eventId']);
```

### ViewContent

File: `app/Http/Resources/ProductResource.php`

Trigger only when:

- Current route is `api.products.show`
- `FACEBOOK_PIXEL_ACTIVATION == '1'`
- request has `eventId`

Event:

```php
'ViewContent'
```

Custom data:

- `content_ids`: product ID as string
- `content_type`: `product`
- `content_name`: product name
- `value`: min base price
- `currency`: setting currency code or USD

### AddToCart

File: `app/Http/Controllers/Frontend/Api/CartController.php`

Trigger after cart save, only for logged-in user and activation on.

Event:

```php
'AddToCart'
```

Use variant ID format:

```php
"{$productVariation->product_id}-{$productVariation->id}"
```

Custom data:

- `value`: `$productVariation->price * $cart->qty`
- `currency`: setting currency code or USD
- `content_ids`: `[variantId]`
- `content_type`: `product`

Pass `$request->eventId`.

### AddToWishlist

File: `app/Http/Controllers/Frontend/Api/WishlistController.php`

Trigger after wishlist save, only for logged-in user and activation on.

Event:

```php
'AddToWishlist'
```

Custom data:

- `content_ids`: product ID as string
- `content_type`: `product_group`
- `content_name`: product name
- `value`: product min price
- `currency`: setting currency code or USD

Pass `$request->eventId`.

### Search

File: `app/Http/Controllers/Frontend/Api/SearchController.php`

Trigger for logged-in user and activation on.

Event:

```php
'Search'
```

Custom data:

- `search_string`: `$request->searchKey`

Current code does not pass an event ID for Search.

### InitiateCheckout

File: `app/Http/Controllers/Frontend/Api/CheckoutController.php`

Endpoint:

```text
POST /checkout/initiate
```

If activation is off, return success with tracking disabled.

Event:

```php
'InitiateCheckout'
```

Input:

- `eventId`
- `value`
- `currency`
- `content_ids`
- `contents`
- `num_items`
- `email`
- `phone`

Custom data:

- `value`
- `currency`
- `content_ids`
- `content_type`: `product`
- `contents`
- `num_items`

Pass `$request->eventId`.

### Purchase

File: `app/Http/Controllers/Frontend/Api/CheckoutController.php`

Trigger after each order is created and saved, only for logged-in user and activation on.

Event:

```php
'Purchase'
```

Use variant ID format:

```php
"{$product->id}-{$variation->id}"
```

Custom data:

- `value`: order total amount
- `currency`: setting currency code or USD
- `content_ids`: array of variant IDs
- `content_type`: `product`
- `contents`: array of `{ id, quantity, item_price }`
- `num_items`: sum of quantities

Pass `$request->eventId`.

## Frontend Ecommerce Events

### Add To Cart

Files:

- `resources/frontend/components/card/product-card/index.tsx`
- `resources/frontend/components/popups/product/ProductPreviewContent.tsx`

Before API call:

```ts
const eventId = createMetaEventId('add_to_cart');
```

Send `eventId` to add-to-cart API.

Push GTM:

```ts
{
  event: 'add_to_cart',
  event_id: eventId,
  ecommerce: {
    currency: window.config.currency.code,
    value: selectedVariation.basePrice,
    items: [{
      item_id: `${productId}-${variationId}`,
      item_name: `${productName} (${variationName})`,
      price: selectedVariation.basePrice,
      quantity: 1
    }]
  }
}
```

### Remove From Cart

Push only to GTM:

```ts
{
  event: 'remove_from_cart',
  ecommerce: {
    currency,
    value,
    items: [{ item_id, item_name, price, quantity }]
  }
}
```

### Add To Wishlist

Create:

```ts
const eventId = createMetaEventId('add_to_wishlist');
```

Send `eventId` to wishlist API.

Push GTM:

```ts
{
  event: 'add_to_wishlist',
  event_id: eventId,
  ecommerce: {
    items: [{ item_id: product.id.toString(), item_name: product.name }]
  }
}
```

### Remove From Wishlist

Push only to GTM:

```ts
{
  event: 'remove_from_wishlist',
  ecommerce: {
    items: [{ item_id: product.id.toString(), item_name: product.name }]
  }
}
```

### Begin Checkout

File: `resources/frontend/pages/order/Checkout.tsx`

When selected checkout products/coupons/shipping change:

- Build a stable cart signature.
- Reuse `initiateCheckoutEventIdRef` once created.
- Create event ID with `createMetaEventId('initiate_checkout')`.
- Push GTM `begin_checkout`.
- POST CAPI `InitiateCheckout` through `trackInitiateCheckout`.

GTM event must include:

- `event`: `begin_checkout`
- `event_id`
- `buildGTM4WPData`
- `cartContent`
- `customer`
- `user_data`
- `ecommerce.currency`
- `ecommerce.value`
- `ecommerce.items`

Server payload must include:

- `eventId`
- `value`
- `currency`
- `content_ids`
- `contents`
- `num_items`
- `email`
- `phone`

### Purchase

File: `resources/frontend/pages/order/Checkout.tsx`

Use the same `eventId` passed to the order creation API.

After successful manual order creation, push GTM:

- `event`: `purchase`
- `event_id`
- `buildGTM4WPData`
- `orderData`
- `new_customer`
- `customer`
- `user_data`
- `ecommerce.transaction_id`
- `ecommerce.currency`
- `ecommerce.value`
- `ecommerce.tax`
- `ecommerce.shipping`
- `ecommerce.coupon`
- `ecommerce.items`

Backend sends Meta CAPI `Purchase` from `CheckoutController` with the same request event ID.

## Facebook Pixel Browser Helper

Copy `resources/frontend/hooks/useFacebookPixelEvents.ts`.

It wraps:

- `AddToCart`
- `Purchase`
- `ViewContent`
- `AddPaymentInfo`
- `AddToWishlist`
- `InitiateCheckout`
- `Search`

When `eventID` exists in event data, call:

```ts
fbq('track', eventName, params, { eventID });
```

Otherwise call:

```ts
fbq('track', eventName, params);
```

## Product ID Convention

Preserve this exactly:

- Product-only events use product ID as string.
- Variant/cart/order events use `productId-variationId`.
- Do not switch to SKU unless the rest of the catalog/feed integration is also changed.

## Activation Behavior

Current behavior is mixed and must be copied as-is:

- Facebook Pixel browser script in `home.blade.php` loads without checking `FACEBOOK_PIXEL_ACTIVATION`.
- Most backend CAPI sends are gated by `FACEBOOK_PIXEL_ACTIVATION == '1'`.
- GTM script loads when `config('services.gtm_id')` is present.
- Admin still has `GOOGLE_ANALYTICS_ACTIVATION`, but GTM loading currently depends on GTM ID, not that activation flag.

## Acceptance Criteria

1. Admin Analytics page saves all listed env keys using the existing env update mechanism.
2. `GTM_ID` present causes GTM head script and body noscript iframe to render.
3. `window.dataLayer` exists after page load.
4. Route changes push one `page_view` dataLayer event per unique path/query.
5. `/tracking/page-view` receives the same page-view event ID that was pushed to dataLayer.
6. Meta CAPI `PageView` sends only when `FACEBOOK_PIXEL_ACTIVATION == '1'`.
7. Add to cart sends event ID to backend and pushes GTM `add_to_cart` with `productId-variationId`.
8. Wishlist add sends event ID to backend and pushes GTM `add_to_wishlist`.
9. Checkout sends GTM `begin_checkout` and backend `InitiateCheckout` with the same event ID.
10. Purchase sends GTM `purchase` and backend `Purchase` with the same event ID.
11. Server CAPI events include `_fbp`, `_fbc`, IP, user agent, email, and phone when available.
12. `FB_TEST_EVENT_CODE` is applied when configured.
13. No new tracking events, renamed events, or changed payload keys are introduced.

## Implementation Notes For Target Codex

- First inspect the target project's equivalent Laravel routes, frontend Blade entry, React app root, API slices, cart/wishlist/checkout controllers, and product resource.
- Reuse target naming/import aliases where needed, but keep payload contracts the same.
- If the target project does not have Redux Toolkit Query, wire the same POST requests through its existing API client.
- If the target does not have `apiUser()`, replace it with the target's authenticated API user helper.
- If the target does not have `getSetting('currencyCode')`, use its current store currency setting, defaulting to `USD`.
- Do not refactor unrelated checkout/cart behavior while copying this feature.
