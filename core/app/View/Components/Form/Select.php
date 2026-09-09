<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Select extends Component
{
    public string $name;

    public ?string $label;

    public ?string $class;

    public array $options;

    public ?string $selected;

    public ?bool $isLabel;

    /**
     * Create a new component instance.
     */
    public function __construct(string $name, ?string $label = null, $class = null, array $options = [], ?string $selected = null, $isLabel = false)
    {

        $this->name     = $name;
        $this->label    = $label;
        $this->class    = $class;
        $this->options  = $options;
        $this->selected = $selected;
        $this->isLabel  = $isLabel;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.form.select');
    }
}
