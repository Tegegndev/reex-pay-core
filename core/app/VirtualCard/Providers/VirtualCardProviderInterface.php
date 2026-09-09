<?php

namespace App\VirtualCard\Providers;

use App\Models\VirtualCardRequest;

interface VirtualCardProviderInterface
{
    /**
     * Issue a card via provider.
     *
     * @return array Card details for DB save
     */
    public function issueCard(VirtualCardRequest $request): array;

    /**
     * Top up a card via provider.
     *
     * @return array Card details for DB save
     */
    public function topUpCard($amount, $cardID): array;

    public function withdrawFromCard($amount, $cardID): array;
}
