<?php

namespace App\View\Components\Backend\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Text extends Component
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
        public $isDisabled  = false,
        public $value       = null,
        public $aiGenerate  = false,
        public $trashBtn = false,
        public $trashParent = '',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.inputs.text');
    }
}
