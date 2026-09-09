<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class Icon extends Component
{
    public $name;

    public $width;

    public $height;

    public $class;

    public function __construct($name, $width = null, $height = null, $class = '')
    {
        $this->name   = $name;
        $this->width  = $width;
        $this->height = $height;
        $this->class  = $class;
    }

    public function render()
    {
        return view('components.icon');
    }

    public function svgContent()
    {
        $cacheKey = "svg.{$this->name}";

        return Cache::rememberForever($cacheKey, function () {
            $svgPath = "{$this->name}.svg";

            return Storage::disk('svg_assets')->get($svgPath);
        });
    }
}
