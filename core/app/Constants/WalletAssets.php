<?php

namespace App\Constants;

class WalletAssets
{
    public const WALLET_CHIP = 'general/static/wallet/chip.png';

    public const WALLET_BACKGROUND = 'general/static/wallet/bg-1.png';

    public const WALLET_BACKGROUND_2 = 'general/static/wallet/bg-2.png';

    /**
     * Get a random wallet background.
     */
    public static function getRandomBackground(): string
    {
        $backgrounds = (new self)->getWalletBackground();

        return asset($backgrounds[array_rand($backgrounds)]);
    }

    /**
     * Get all wallet backgrounds.
     */
    public function getWalletBackground(): array
    {
        return [
            self::WALLET_BACKGROUND,
            self::WALLET_BACKGROUND_2,
        ];
    }
}
