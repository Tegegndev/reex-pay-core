<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Field extends Component
{
    public $type;

    public $name;

    public $label;

    public $placeholder;

    public $value;

    public $required;

    public $colClass;

    public function __construct($type, $name, $label, $placeholder = null, $value = null, $required = false, $colClass = null)
    {
        $this->type        = $type;
        $this->name        = $name;
        $this->label       = $label;
        $this->placeholder = $placeholder ?? $label;
        $this->value       = $value;
        $this->required    = $required;
        $this->colClass    = $colClass;
    }

    public function render()
    {
        return view('components.form.field');
    }
}
