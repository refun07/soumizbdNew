<?php
namespace App\Data\Orders;

class RedxOrderData
{
    public function __construct(
        public string $customer_name,
        public string $customer_phone,
        public string $delivery_area,
        public int $delivery_area_id,
        public string $customer_address,
        public string $cash_collection_amount,
        public string $parcel_weight,//Weight of the parcel in appropriate units (e.g., kg, g).
        public ?string $merchant_invoice_id,
        public ?string $instruction,
        public ?string $type,//Defines the parcel type, mainly used for reverse shipments.
        public string $value,
    ) {}
}
