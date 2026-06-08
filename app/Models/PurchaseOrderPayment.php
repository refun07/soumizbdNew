<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderPayment extends Model
{
    use HasFactory;

    public function payable()
    {
        return $this->morphTo();
    }
}
