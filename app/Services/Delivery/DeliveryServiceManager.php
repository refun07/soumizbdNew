<?php
namespace App\Services\Delivery;

use App\Services\Interfaces\DeliveryPartnerInterface;

class DeliveryServiceManager
{
    protected DeliveryPartnerInterface $partner;

    public function __construct(DeliveryPartnerInterface $partner)
    {
        $this->partner = $partner;
    }

    public function createOrder(object $data): array
    {
        return $this->partner->createOrder($data);
    }

    public function bulkCreateOrders(array $orders)
    {
        return $this->partner->bulkCreateOrders($orders);
    }

    public function getDeliveryStatus(string $trackingId)
    {
        return $this->partner->getDeliveryStatus($trackingId);
    }

    public function getBalance()
    {
        return $this->partner->getBalance();
    }
}
