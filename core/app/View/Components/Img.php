<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Img extends Component
{
    public ?string $name;

    public string $old;

    public string $ref;

    public string $id;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $name = null, ?string $old = null, string $ref = '')
    {
        $this->name = $name;
        $this->ref  = $ref;

        $this->old = (! empty($old))
            ? asset($old)
            : asset('general/static/default/placeholder.png');

        $this->id = $this->generateId($name, $ref);
    }

    /**
     * Generate unique ID from name and ref.
     */
    protected function generateId(?string $name, string $ref): string
    {
        if (is_null($name)) {
            return $ref;
        }

        return str_contains($name, '[') || str_contains($name, ']')
            ? str_replace(['[', ']'], '', $name).$ref
            : $name.$ref;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.img');
    }
}
