<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA;
    }

    /**
     * Generate a new secret key.
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Verify a 6-digit code against a secret.
     */
    public function verifyCode(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code, 2);
    }

    /**
     * Generate a data URI for a QR code (SVG) that encodes the secret.
     *
     * @param string $secret The Google 2FA secret key
     * @param string $label  Typically user's email or name
     */
    public function generateQrCode(string $secret, string $label): string
    {
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(setting('site_title'), $label, $secret);

        $renderer = new ImageRenderer(new RendererStyle(200),
            new SvgImageBackEnd);

        $writer  = new Writer($renderer);
        $svgData = $writer->writeString($qrCodeUrl);

        return 'data:image/svg+xml;base64,'.base64_encode($svgData);
    }
}
