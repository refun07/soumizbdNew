<?php
namespace App\Data\Orders;

class PathaoOrderData
{
    public function __construct(
        public int $store_id,
        public ?string $merchant_order_id,
        public string $recipient_name,
        public string $recipient_phone,
        public string $recipient_address,
        public int $recipient_city,
        public int $recipient_zone,
        public ?int $recipient_area,
        public int $delivery_type, //48 for Normal Delivery, 12 for On Demand Delivery
        public int $item_type,//1 for Document, 2 for Parcel
        public int $item_quantity,
        public float $item_weight,//Minimum 0.5 KG to Maximum 10 kg. Weight of your parcel in kg
        public ?string $item_description,
        public int $amount_to_collect, //Recipient Payable Amount. Default should be 0 in case of NON Cash-On-Delivery(COD)The collectible amount from the customer.
        public ?string $special_instruction = null,
    ) {}
}
