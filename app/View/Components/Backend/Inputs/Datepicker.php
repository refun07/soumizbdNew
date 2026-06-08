<?php

namespace App\View\Components\Backend\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Datepicker extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct(
        public $name,
        public $label       = '',
        public $labelInline = true,
        public $placeholder = '',
        public $isRequired  = true,
        public $type        = 'single', // range
        public $isDisabled  = false,
        public $htmlDateInput  = false,
        public $value       = null
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.inputs.datepicker');
    }
}
