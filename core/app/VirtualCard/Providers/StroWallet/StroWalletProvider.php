<?php

namespace App\VirtualCard\Providers\StroWallet;

use App\Enums\VirtualCard\CardholderType;
use App\Exceptions\NotifyErrorException;
use App\Models\Cardholders;
use App\Models\VirtualCardRequest;
use App\VirtualCard\Providers\VirtualCardProviderInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class StroWalletProvider implements VirtualCardProviderInterface
{
    private $credentials;

    private $httpClient;

    public function __construct()
    {
        $this->credentials = \App\Models\PaymentGateway::getCredentials('strowallet');
        $this->httpClient  = new Client;

        if (empty($this->credentials['public_key']) || empty($this->credentials['secret_key'])) {
            throw new \Exception('StroWallet API credentials are not configured.');
        }
    }

    public function issueCard(VirtualCardRequest $request): array
    {
        $customerData = $this->resolveCustomerData($request);

        $this->createCustomer($customerData);

        $cardData = [
            'name_on_card'  => $request->user->name,
            'card_type'     => 'visa',
            'public_key'    => $this->credentials['public_key'],
            'amount'        => $request->amount ?? 5,
            'customerEmail' => $this->credentials['mode'] === 'sandbox' ? 'mydemo@gmail.com' : $customerData['customerEmail'],
            'mode'          => $this->credentials['mode'],
        ];

        $cardResponse = $this->createCard($cardData);

        return [
            'id'           => $cardResponse['card_id'],
            'last4'        => $cardResponse['last4'],
            'brand'        => $cardResponse['brand'],
            'expiry_month' => $cardResponse['expiry_month'],
            'expiry_year'  => $cardResponse['expiry_year'],
            'status'       => $cardResponse['status'],
            'meta'         => $cardResponse['meta'],
        ];
    }

    public function topUpCard($amount, $cardID): array
    {
        // Prepare API credentials
        $publicKey = $this->credentials['public_key'];
        $mode      = $this->credentials['mode'];

        try {
            $response = $this->httpClient->post('https://strowallet.com/api/bitvcard/fund-card/', [
                'json' => [
                    'card_id'    => $cardID,
                    'amount'     => $amount,
                    'public_key' => $publicKey,
                    'mode'       => $mode,
                ],
                'headers' => [
                    'accept'       => 'application/json',
                    'content-type' => 'application/json',
                ],
                'timeout' => 15,
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (! isset($responseData['success']) || ! $responseData['success']) {
                Log::error('StroWallet top-up error', ['response' => $responseData]);
                throw new \Exception($responseData['message'] ?? 'StroWallet top-up failed.');
            }

            // Return the relevant API response data for DB save or further logic
            return [
                'id'        => $responseData['apiresponse']['data']['id']        ?? null,
                'status'    => $responseData['apiresponse']['data']['status']    ?? null,
                'cardId'    => $responseData['apiresponse']['data']['cardId']    ?? null,
                'reference' => $responseData['apiresponse']['data']['reference'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('StroWallet top-up exception', ['error' => $e->getMessage()]);
            throw new \Exception('StroWallet API top-up error: '.$e->getMessage());
        }
    }

    public function withdrawFromCard($amount, $cardID): array
    {
        $publicKey = $this->credentials['public_key'];
        $mode      = $this->credentials['mode'];

        try {
            $response = $this->httpClient->post('https://strowallet.com/api/bitvcard/card_withdraw/', [
                'json' => [
                    'card_id'    => $cardID,
                    'amount'     => $amount,
                    'public_key' => $publicKey,
                    'mode'       => $mode,
                ],
                'headers' => [
                    'accept'       => 'application/json',
                    'content-type' => 'application/json',
                ],
                'timeout' => 15,
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (! isset($responseData['success']) || ! $responseData['success']) {
                Log::error('StroWallet withdraw error', ['response' => $responseData]);
                throw new \Exception($responseData['message'] ?? 'StroWallet withdraw failed.');
            }

            // Return the relevant API response data for DB save or further logic
            return [
                'id'        => $responseData['apiresponse']['data']['id']        ?? null,
                'status'    => $responseData['apiresponse']['data']['status']    ?? null,
                'cardId'    => $responseData['apiresponse']['data']['cardId']    ?? null,
                'reference' => $responseData['apiresponse']['data']['reference'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('StroWallet withdraw exception', ['error' => $e->getMessage()]);
            throw new \Exception('StroWallet API withdraw error: '.$e->getMessage());
        }
    }

    private function resolveCustomerData(VirtualCardRequest $request): array
    {
        $ch         = Cardholders::with('business')->with('user')->findOrFail($request->cardholder_id);
        $isBusiness = $ch->card_type === CardholderType::BUSINESS;

        if ($isBusiness) {
            throw new NotifyErrorException('StroWallet only supports personal accounts, not business accounts.');
        }
        $KycFileField = collect($ch->kycTemplate->fields)->firstWhere('type', 'file');

        return [
            'public_key'    => $this->credentials['public_key'],
            'firstName'     => $ch->first_name,
            'lastName'      => $ch->last_name,
            'customerEmail' => $ch->email,
            'phoneNumber'   => $ch->mobile,
            'dob'           => $ch->dob ? (new \DateTime($ch->dob))->format('m/d/Y') : null,
            'idImage'       => asset($ch->kyc_documents[$KycFileField['label']]),
            'userPhoto'     => asset($ch->user->avatar_alt),
            'street'        => $ch->address_line1,
            'state'         => $ch->state,
            'zipCode'       => $ch->postal_code,
            'city'          => $ch->city,
            'country'       => $ch->country,
            'idType'        => $ch->kycTemplate->title,
        ];

    }

    private function createCustomer(array $data): array
    {
        $response = $this->httpClient->post('https://strowallet.com/api/bitvcard/create-user', [
            'json' => [
                'public_key'    => $data['public_key'],
                'houseNumber'   => $data['street'],
                'firstName'     => $data['firstName'],
                'lastName'      => $data['lastName'] ?? '',
                'idNumber'      => $data['idType'],
                'customerEmail' => $data['customerEmail'],
                'phoneNumber'   => $data['phoneNumber'],
                'dateOfBirth'   => $data['dob'] ?? '',
                'idImage'       => $data['idImage'],
                'userPhoto'     => $data['userPhoto'],
                'line1'         => $data['street'],
                'state'         => $data['state'],
                'zipCode'       => $data['zipCode'],
                'city'          => $data['city'],
                'country'       => $data['country'],
                'idType'        => $data['idType'],
            ],
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        $response_data = json_decode($response->getBody(), true);
        if (isset($response_data['error']) && $response_data['error'] !== 'ok') {
            Log::error('StroWallet customer creation error', ['response' => $response_data]);
            throw new \Exception('StroWallet customer creation failed.');
        }

        return $response_data;
    }

    private function createCard(array $data): array
    {
        $response = $this->httpClient->post('https://strowallet.com/api/bitvcard/create-card/', [
            'json' => [
                'name_on_card'  => $data['name_on_card'],
                'card_type'     => $data['card_type'],
                'public_key'    => $data['public_key'],
                'amount'        => $data['amount'],
                'customerEmail' => $data['customerEmail'],
                'mode'          => $data['mode'],
            ],
            'headers' => [
                'accept'       => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);

        $response_data = json_decode($response->getBody(), true);

        if (isset($response_data['error']) && $response_data['error'] !== 'ok') {
            Log::error('StroWallet card issuance error', ['response' => $response_data]);
            throw new \Exception('StroWallet card issuance failed.');
        }

        $response_data = $response_data['response'];

        $cardDetails = $this->fetchCardDetails($response_data['card_id'])['card_detail'];

        $meta = [
            'card_id'      => $cardDetails['card_id'],
            'card_user_id' => $cardDetails['card_user_id'],
            'customer_id'  => $cardDetails['customer_id'],
        ];
        [$expiryMonth, $expiryYear] = explode('/', $cardDetails['expiry']);

        return [
            'card_id'      => $cardDetails['card_id'],
            'last4'        => $cardDetails['last4'],
            'brand'        => $cardDetails['card_brand'],
            'expiry_month' => $expiryMonth,
            'expiry_year'  => $expiryYear,
            'status'       => $response_data['card_status'],
            'meta'         => $meta,
        ];
    }

    private function fetchCardDetails(string $cardId): array
    {
        $response = $this->httpClient->post('https://strowallet.com/api/bitvcard/fetch-card-detail/', [
            'json' => [
                'public_key' => $this->credentials['public_key'],
                'mode'       => $this->credentials['mode'],
                'card_id'    => $cardId,
            ],
            'headers' => [
                'accept'       => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);

        $response_data = json_decode($response->getBody(), true);

        if (isset($response_data['error']) && $response_data['error'] !== 'ok') {
            Log::error('StroWallet card details fetch error', ['response' => $response_data]);
            throw new \Exception('StroWallet card details fetch failed.');
        }

        return $response_data['response'];
    }
}
