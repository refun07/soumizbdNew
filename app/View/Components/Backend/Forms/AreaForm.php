<?php

namespace App\View\Components\Backend\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AreaForm extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $cities      = [],
        public $zones       = [],
        public $area        = null,
        public $langKey     = null
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.forms.area-form');
    }
}
