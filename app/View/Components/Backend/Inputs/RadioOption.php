<?php

namespace App\View\Components\Backend\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RadioOption extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $name,
        public $value,
        public $checkedValue,
        public $label = '',
        public $type = 'radio',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.inputs.radio-option');
    }
}
