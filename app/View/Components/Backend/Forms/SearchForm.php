<?php

namespace App\View\Components\Backend\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchForm extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $class       = 'min-w-[280px] max-w-[280px]',
        public $searchKey   = '',
        public $placeholder = 'Type name & hit enter',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.forms.search-form');
    }
}
