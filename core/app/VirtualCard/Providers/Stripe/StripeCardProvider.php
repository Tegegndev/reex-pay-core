<?php

namespace App\VirtualCard\Providers\Stripe;

use App\Models\Cardholders;
use App\Models\PaymentGateway;
use App\Models\VirtualCardRequest;
use App\VirtualCard\Providers\VirtualCardProviderInterface;
use Stripe\StripeClient;

class StripeCardProvider implements VirtualCardProviderInterface
{
    /**
     * Issue a virtual card with Stripe for the given request.
     * Handles both personal and business cardholders.
     */
    public function issueCard(VirtualCardRequest $request): array
    {
        $cardholderData = $this->resolveCardholderData($request);

        $credentials = PaymentGateway::getCredentials('stripe');
        $stripe      = new StripeClient($credentials['stripe_secret']);

        // 1. Create cardholder in Stripe
        $stripeCardholder = $stripe->issuing->cardholders->create([
            'name'    => $cardholderData['name'],
            'email'   => $cardholderData['email'],
            'type'    => $cardholderData['stripe_type'], // 'individual' or 'company'
            'billing' => [
                'address' => [
                    'line1'       => $cardholderData['address_line1'],
                    'city'        => $cardholderData['city'],
                    'state'       => $cardholderData['state'],
                    'country'     => $cardholderData['country'],
                    'postal_code' => $cardholderData['postal_code'],
                ],
            ],
        ]);

        // 2. Issue the card
        $stripeCard = $stripe->issuing->cards->create([
            'cardholder' => $stripeCardholder->id,
            'currency'   => $request->wallet->currency->code,
            'type'       => 'virtual',
        ]);

        return [
            'id'           => $stripeCard->id,
            'last4'        => $stripeCard->last4,
            'brand'        => $stripeCard->brand,
            'expiry_month' => $stripeCard->exp_month,
            'expiry_year'  => $stripeCard->exp_year,
            'status'       => $stripeCard->status,
            'meta'         => [
                'stripe_cardholder_id' => $stripeCardholder->id,
                'stripe_card_id'       => $stripeCard->id,
            ],
            'raw' => $stripeCard,
        ];
    }

    /**
     * Resolve all required cardholder data for Stripe (personal or business).
     */
    private function resolveCardholderData(VirtualCardRequest $request): array
    {
        $ch         = Cardholders::with('business')->findOrFail($request->cardholder_id);
        $isBusiness = $ch->card_type && method_exists($ch->card_type, 'isBusiness') && $ch->card_type->isBusiness();

        if ($isBusiness && $ch->business) {
            $b = $ch->business;

            return [
                'name'          => $b->business_name,
                'email'         => $b->contact_email ?? $ch->email,
                'stripe_type'   => 'company',
                'address_line1' => $b->address_line1 ?? $ch->address_line1,
                'city'          => $b->city          ?? $ch->city,
                'state'         => $b->state         ?? $ch->state,
                'country'       => $b->country       ?? $ch->country,
                'postal_code'   => $b->postal_code   ?? $ch->postal_code,
            ];
        } else {
            return [
                'name'          => $ch->full_name ?? ($ch->first_name.' '.$ch->last_name),
                'email'         => $ch->email,
                'stripe_type'   => 'individual',
                'address_line1' => $ch->address_line1,
                'city'          => $ch->city,
                'state'         => $ch->state,
                'country'       => $ch->country,
                'postal_code'   => $ch->postal_code,
            ];
        }
    }

    public function topUpCard($amount, $cardID): array
    {
        // TODO: Implement topUpCard() method.
    }

    public function withdrawFromCard($amount, $cardID): array
    {
        // TODO: Implement withdrawFromCard() method.
    }
}
