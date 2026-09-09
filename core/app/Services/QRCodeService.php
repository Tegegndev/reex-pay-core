<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QRCodeService
{
    /**
     * Generate a QR code as SVG.
     *
     * @param  string $text The text or URL to encode in the QR code.
     * @param  int    $size The width/height of the QR code (pixels).
     * @return string SVG markup for the QR code.
     */
    public function generate(string $text, int $size = 220): string
    {
        // Create a QR code renderer with the given size and SVG backend
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd
        );

        // Writer generates the SVG QR code as a string
        $writer = new Writer($renderer);

        return $writer->writeString($text);
    }
}
