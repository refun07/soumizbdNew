<?php

namespace App\View\Components\Backend\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $name,
        public $label = '',
        public $labelInline = true,
        public $wrapperClass = '',
        public $groupClass = '',
        public $isRequired = true,
        public $isDisabled = false,
        public $type = 'initSelect2',
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
        return view('components.backend.inputs.select');
    }
}
