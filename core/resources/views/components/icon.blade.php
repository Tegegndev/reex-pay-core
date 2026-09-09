@php use App\View\Components\Icon; @endphp
@props(['name', 'width' => null, 'height' => null, 'class' => ''])

@php
    $svgContent = (new Icon($name))->svgContent();

    if ($svgContent) {
        $dom = new DOMDocument();
        $dom->loadXML($svgContent);
        $svg = $dom->getElementsByTagName('svg')->item(0);

        if ($width) {
            $svg->setAttribute('width', $width);
        }

        if ($height) {
            $svg->setAttribute('height', $height);
        }

        if ($class) {
            $existingClasses = $svg->getAttribute('class');
            $svg->setAttribute('class', trim("$existingClasses $class"));
        }

        $svgContent = $dom->saveXML($svg);
    }
@endphp

@if ($svgContent)
    {!! $svgContent !!}
@else
    {{-- Optional: Handle the case where the SVG file does not exist --}}
    <span>{{ __('Icon not found') }}: {{ $name }}</span>
@endif
