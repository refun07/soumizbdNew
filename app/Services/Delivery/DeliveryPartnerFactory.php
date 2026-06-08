<?php
namespace App\Services\Delivery;

use App\Services\Interfaces\DeliveryPartnerInterface;

class DeliveryPartnerFactory
{
    public static function make(string $partner): DeliveryServiceManager
    {
        return match (strtolower($partner)) {
            'steadfast' => new DeliveryServiceManager(new SteadfastService()),
            'redx'      => new DeliveryServiceManager(new RedxService()),
            'pathao'    => new DeliveryServiceManager(new PathaoCourierService()),
            default     => throw new \Exception("Unsupported delivery partner: {$partner}"),
        };
    }
}
