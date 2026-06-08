<?php
namespace App\Data\Orders;

class SteadfastOrderData
{
    public function __construct(
        public string $invoice,
        public string $recipient_name,
        public string $recipient_phone,
        public ?string $recipient_email,
        public string $recipient_address,
        public float $cod_amount,
        // public ?string $recipient_city = null,
        // public ?string $recipient_area = null,
        public ?string $note = null,
        public ?int $delivery_type,//0 = for home delivery, 1 = for Point Delivery/Steadfast Hub Pick Up
        public ?int $total_lot,
    ) {}
}

