<?php
namespace App\Services;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;

// class FacebookConversionService
// {
//     protected $pixelId;
//     protected $accessToken;
//     protected $api;

//     public function __construct()
//     {
//         $this->accessToken = env('META_CONVERSIONS_API_ACCESS_TOKEN');
//         $this->pixelId = env('META_PIXEL_ID');
//         $this->api = Api::init(null, null, $this->accessToken);
//         $this->api->setLogger(new CurlLogger());
//     }

//     public function sendEvent(string $eventName, array $userInfo = [], array $customData = [])
//     {
//         if(config('app.FACEBOOK_PIXEL_ACTIVATION') == '0' ){
//             return;
//         }
//         $fbc = request()->cookie('_fbc') ? request()->cookie('_fbc'):$_COOKIE['_fbc'] ?? null;
//         $fbp = request()->cookie('_fbp') ? request()->cookie('_fbp'):$_COOKIE['_fbp'] ?? null;
//         $userData = (new UserData())
//             ->setEmail($userInfo['email'] ?? null)
//             ->setPhone($userInfo['phone'] ?? null)
//             ->setClientIpAddress(request()->ip())
//             ->setClientUserAgent(request()->userAgent())
//             ->setFbc($fbc)
//             ->setFbp($fbp);


//         $customDataObj = (new CustomData())
//             ->setValue($customData['value'] ?? null)
//             ->setCurrency($customData['currency'] ?? 'USD');

//         $event = (new Event())
//             ->setEventName($eventName)
//             ->setEventTime(time())
//             ->setUserData($userData)
//             ->setCustomData($customDataObj)
//             ->setActionSource('website');

//         $request = (new EventRequest($this->pixelId))
//             ->setEvents([$event]);

//         return $request->execute();
//     }
// }


class FacebookConversionService
{
    protected $pixelId;
    protected $accessToken;
    protected bool $configured = false;

    public function __construct()
    {
        $this->pixelId     = (string) config('app.META_PIXEL_ID');
        $this->accessToken = (string) config('app.META_CONVERSIONS_API_ACCESS_TOKEN');

        if (config('app.FACEBOOK_PIXEL_ACTIVATION') != '1') {
            return;
        }

        if (! preg_match('/^\d+$/', $this->pixelId) || $this->accessToken === '') {
            return;
        }

        Api::init(null, null, $this->accessToken);
        Api::instance()->setLogger(new CurlLogger());
        $this->configured = true;
    }

    public function sendEvent(
        string $eventName,
        array $userInfo = [],
        array $customData = [],
        ?string $eventId = null    // for deduplication
    ) {
        if (config('app.FACEBOOK_PIXEL_ACTIVATION') != '1') {
            return;
        }

        if (! $this->configured) {
            throw new \RuntimeException('Meta CAPI is active but its Pixel ID or access token is invalid.');
        }

        // Collect fbp / fbc
        $fbc = request()->cookie('_fbc') ?? ($_COOKIE['_fbc'] ?? null);
        $fbp = request()->cookie('_fbp') ?? ($_COOKIE['_fbp'] ?? null);

        // Build user data
        $userData = (new UserData())
            ->setEmail($userInfo['email'] ?? null)
            ->setPhone($userInfo['phone'] ?? null)
            ->setClientIpAddress(request()->ip())
            ->setClientUserAgent(request()->userAgent())
            ->setFbp($fbp)
            ->setFbc($fbc);

        // Build contents array if provided
        $contentsArr = [];
        if (!empty($customData['contents'])) {
            foreach ($customData['contents'] as $item) {
                $contentsArr[] = (new Content())
                    ->setProductId($item['id'])
                    ->setQuantity($item['quantity'])
                    ->setItemPrice($item['item_price']);
            }
        }

        $customDataObj = new CustomData();

        if (isset($customData['currency'])) {
            $customDataObj->setCurrency(strtoupper($customData['currency']));
        }

        if (array_key_exists('value', $customData)) {
            $customDataObj->setValue($customData['value']);
        }

        if (!empty($customData['content_ids'])) {
            $customDataObj->setContentIds($customData['content_ids']);
        }

        if (!empty($contentsArr)) {
            $customDataObj->setContents($contentsArr);
        }

        if (!empty($customData['content_type'])) {
            $customDataObj->setContentType($customData['content_type']);
        }

        if (!empty($customData['content_name'])) {
            $customDataObj->setContentName($customData['content_name']);
        }

        if (array_key_exists('num_items', $customData)) {
            $customDataObj->setNumItems((int) $customData['num_items']);
        }

        if (!empty($customData['order_id'])) {
            $customDataObj->setOrderId((string) $customData['order_id']);
        }

        // Build server event
        $event = (new Event())
            ->setEventName($eventName)
            ->setEventTime(time())
            ->setUserData($userData)
            ->setCustomData($customDataObj)
            ->setActionSource(ActionSource::WEBSITE)
            ->setEventSourceUrl($customData['event_source_url'] ?? url()->current())
            ->setEventId($eventId); // dedupe

        // Build request
        $request = (new EventRequest($this->pixelId))
            ->setEvents([$event]);

        // Optional test event code
        if (config('app.FB_TEST_EVENT_CODE')) {
            $request->setTestEventCode(config('app.FB_TEST_EVENT_CODE'));
        }

        return $request->execute();
    }
}
