<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubmitButton extends Component
{
    public string $icon;

    public string $color;

    public string $type;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $icon = 'check',
        string $color = 'btn-primary',
        string $type = 'submit'
    ) {
        $this->icon  = $icon;
        $this->color = $color;
        $this->type  = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.submit-button');
    }
}
