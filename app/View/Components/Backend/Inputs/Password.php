<?php

namespace App\View\Components\Backend\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Password extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct(
        public $name,
        public $placeholder = '',
        public $label       = '',
        public $labelInline = true,
        public $isRequired  = true,
        public $isDisabled  = false
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.inputs.password');
    }
}
