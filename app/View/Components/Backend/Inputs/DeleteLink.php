<?php

namespace App\View\Components\Backend\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteLink extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $href    = "",
        public $title   = "Are you sure you want to delete this item?",
        public $text    = "All data related to this may get deleted.",
        public $btnText = "Delete",
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.inputs.delete-link');
    }
}
