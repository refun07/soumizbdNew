<?php

namespace App\View\Components\Backend\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PurchaseOrderForm extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $purchaseOrder   = null,
        public $suppliers       = [],
        public $warehouses      = [],
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.forms.purchase-order-form');
    }
}
