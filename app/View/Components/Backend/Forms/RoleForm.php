<?php

namespace App\View\Components\Backend\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RoleForm extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $permissionGroups = [],
        public $role = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.forms.role-form');
    }
}
