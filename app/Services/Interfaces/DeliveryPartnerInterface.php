<?php
namespace App\Services\Interfaces;

interface DeliveryPartnerInterface
{
   public function createOrder(object $data): array;

    // public function bulkCreateOrders(array $orders): array;
    // public function getDeliveryStatus(string $trackingId): array;
    // public function getBalance(): array;
}

