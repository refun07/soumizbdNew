<?php

namespace App\View\Components\Backend\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductForm extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $categories           = [],
        public $brands               = [],
        public $units                = [],
        public $variations           = [],
        public $taxes                = [],
        public $tags                 = [],
        public $badges               = [],
        public $shippingClasses      = [],
        public $product              = null,
        public $langKey              = null
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.forms.product-form');
    }
}
