<?php

namespace App\View\Components\Backend\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $variant = "primary", // 'primary', 'secondary', 'success', 'danger', 'warning', 'light', 'dark'
        public $href = ""
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.inputs.link');
    }
}
