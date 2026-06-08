<?php

namespace App\View\Components\Backend\Inputs;

use App\Models\Language;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChangeLanguages extends Component
{
    public $language;
    public $languages;

    /**
     * Create a new component instance.
     */
    public function __construct($langKey)
    {
        $language   = Language::isActive()->where('code', $langKey)->first();
        if (is_null($language)) {
            $language   = Language::isActive()->where('code', 'en-US')->first();
        }
        $this->language = $language;

        $this->languages  = Language::isActive()->latest()->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.inputs.change-languages');
    }
}
