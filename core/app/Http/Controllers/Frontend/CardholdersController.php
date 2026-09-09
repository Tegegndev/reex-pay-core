<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\Gender;
use App\Enums\KycStatus;
use App\Enums\VirtualCard\CardholderType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreCardholderRequest;
use App\Http\Requests\Frontend\UpdateCardholderRequest;
use App\Models\Businesses;
use App\Models\Cardholders;
use App\Models\KycTemplate;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardholdersController extends Controller
{
    use FileManageTrait;

    // region Cardholder CRUD
    /**
     * Display a listing of the cardholders.
     */
    public function index()
    {
        $cardholders = Cardholders::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('frontend.user.virtual_card.cardholders.index', compact('cardholders'));
    }

    /**
     * Show the form for creating a new cardholder.
     */
    public function create()
    {
        $cardholderType = CardholderType::cases();
        $kycTemplates   = KycTemplate::active()->get();
        $businesses     = $this->getUserBusinesses();
        $allCountries   = function_exists('getCountries') ? getCountries() : [];
        $genderOptions  = Gender::options();

        return view('frontend.user.virtual_card.cardholders.create', compact(
            'cardholderType', 'kycTemplates', 'businesses', 'allCountries', 'genderOptions'
        ));
    }

    /**
     * Store a newly created cardholder in storage.
     */
    public function store(StoreCardholderRequest $request)
    {
        $validated            = $request->validated();
        $validated['user_id'] = Auth::id();

        if ($validated['card_type'] === CardholderType::BUSINESS->value) {
            $business                   = $this->createBusinessFromRequest($validated);
            $validated['businesses_id'] = $business->id;
            $validated                  = $this->filterBusinessCardholderData($validated);
        } else {
            $validated = $this->filterPersonalCardholderData($validated);
            $validated = $this->handlePersonalKyc($validated, $request);
        }

        Cardholders::create($validated);
        notifyEvs('success', __('Cardholder created successfully.'));

        return redirect()->route('user.virtual-card.cardholders.index');
    }

    /**
     * Display the specified cardholder.
     */
    public function show(int $id)
    {
        $cardholder = $this->findUserCardholder($id);

        return view('frontend.user.virtual_card.cardholders.show', compact('cardholder'));
    }

    /**
     * Show the form for editing the specified cardholder.
     */
    public function edit(int $id)
    {
        $cardholder     = $this->findUserCardholder($id);
        $businesses     = $this->getUserBusinesses();
        $kycTemplates   = KycTemplate::active()->get();
        $allCountries   = function_exists('getCountries') ? getCountries() : [];
        $cardholderType = CardholderType::cases();
        $genderOptions  = Gender::options();

        return view('frontend.user.virtual_card.cardholders.edit', compact(
            'cardholder', 'businesses', 'kycTemplates', 'allCountries', 'cardholderType', 'genderOptions'
        ));
    }

    /**
     * Update the specified cardholder in storage.
     */
    public function update(UpdateCardholderRequest $request, int $id)
    {
        $cardholder = $this->findUserCardholder($id);
        $validated  = $request->validated();

        if (! $cardholder->status->isPending()) {
            notifyEvs('warning', __('Not Allowed to update cardholder.'));

            return redirect()->route('user.virtual-card.cardholders.index');
        }

        if ($validated['card_type'] === CardholderType::BUSINESS->value) {
            $business = $cardholder->business;
            if ($business) {
                $business->update([
                    'business_name'       => $validated['business_name']       ?? $business->business_name,
                    'registration_number' => $validated['registration_number'] ?? $business->registration_number,
                    'tin'                 => $validated['tin']                 ?? $business->tin,
                    'business_type'       => $validated['business_type']       ?? $business->business_type,
                    'contact_email'       => $validated['contact_email']       ?? $business->contact_email,
                    'contact_phone'       => $validated['contact_phone']       ?? $business->contact_phone,
                    'address_line1'       => $validated['address_line1_b']     ?? $business->address_line1,
                    'address_line2'       => $validated['address_line2_b']     ?? $business->address_line2,
                    'city'                => $validated['city_b']              ?? $business->city,
                    'state'               => $validated['state_b']             ?? $business->state,
                    'postal_code'         => $validated['postal_code_b']       ?? $business->postal_code,
                    'country'             => $validated['country_b']           ?? $business->country,
                ]);
            }
            $validated = $this->filterBusinessCardholderData($validated);
        } else {
            $validated = $this->filterPersonalCardholderData($validated);
            $validated = $this->handlePersonalKyc($validated, $request, $cardholder);
        }

        $cardholder->update($validated);
        notifyEvs('success', __('Cardholder updated successfully.'));

        return redirect()->route('user.virtual-card.cardholders.index');
    }

    /**
     * Remove the specified cardholder from storage.
     */
    public function destroy(int $id)
    {
        $cardholder = $this->findUserCardholder($id);

        if ($cardholder->status->isApproved()) {
            notifyEvs('warning', __('Not Allowed to delete cardholder.'));

            return redirect()->route('user.virtual-card.cardholders.index');
        }

        if ($cardholder->business) {
            $cardholder->business->delete();
        }
        $cardholder->delete();
        notifyEvs('success', __('Cardholder deleted successfully.'));

        return redirect()->route('user.virtual-card.cardholders.index');
    }
    // endregion

    // region Private Helpers
    /**
     * Get all active businesses for the authenticated user.
     */
    private function getUserBusinesses(): \Illuminate\Support\Collection
    {
        return Businesses::where('user_id', Auth::id())
            ->where('status', true)
            ->orderBy('business_name')
            ->get();
    }

    /**
     * Find a cardholder belonging to the authenticated user.
     */
    private function findUserCardholder(int $id): Cardholders
    {
        return Cardholders::where('user_id', Auth::id())->findOrFail($id);
    }

    /**
     * Create a business from validated request data.
     */
    private function createBusinessFromRequest(array $validated): Businesses
    {
        return Businesses::create([
            'user_id'             => Auth::id(),
            'business_name'       => $validated['business_name']       ?? null,
            'registration_number' => $validated['registration_number'] ?? null,
            'tin'                 => $validated['tin']                 ?? null,
            'business_type'       => $validated['business_type']       ?? null,
            'contact_email'       => $validated['contact_email']       ?? null,
            'contact_phone'       => $validated['contact_phone']       ?? null,
            'address_line1'       => $validated['address_line1_b']     ?? null,
            'address_line2'       => $validated['address_line2_b']     ?? null,
            'city'                => $validated['city_b']              ?? null,
            'state'               => $validated['state_b']             ?? null,
            'postal_code'         => $validated['postal_code_b']       ?? null,
            'country'             => $validated['country_b']           ?? null,
        ]);
    }

    /**
     * Handle KYC logic for personal cardholders.
     */
    private function handlePersonalKyc(array $validated, Request $request, ?Cardholders $existingCardholder = null): array
    {
        if ($existingCardholder == null || ($existingCardholder->kyc_status === KycStatus::REJECTED->value)) {
            $kycTemplateId = $request->input('kyc_template_id');
            if ($kycTemplateId) {
                $validated['kyc_type']   = $kycTemplateId;
                $validated['kyc_status'] = KycStatus::PENDING->value;
                $kycDocuments            = [];
                $kycTemplate             = KycTemplate::active()->find($kycTemplateId);
                if ($kycTemplate) {
                    foreach ($kycTemplate->fields as $field) {
                        $fieldKey = "credentials.{$field['label']}";
                        $value    = $request->input($fieldKey);
                        if (isset($field['type']) && $field['type'] === 'file' && $request->hasFile($fieldKey)) {
                            $file  = $request->file($fieldKey);
                            $value = $this->uploadFile($file);
                        }
                        $kycDocuments[$field['label']] = $value;
                    }
                }
                $validated['kyc_documents'] = $kycDocuments ?: null;
            } elseif ($existingCardholder) {
                $validated['kyc_type']      = $existingCardholder->kyc_type;
                $validated['kyc_status']    = $existingCardholder->kyc_status;
                $validated['kyc_documents'] = $existingCardholder->kyc_documents;
            }
        } else {
            unset($validated['kyc_type'], $validated['kyc_status'], $validated['kyc_documents']);
        }

        return $validated;
    }

    /**
     * Only keep fields relevant for personal cardholder.
     */
    private function filterPersonalCardholderData(array $validated): array
    {
        return collect($validated)->only([
            'user_id', 'first_name', 'last_name', 'email', 'mobile', 'gender', 'dob', 'relation',
            'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country',
            'card_type', 'kyc_status', 'kyc_type', 'address_proof_type', 'kyc_documents', 'status',
        ])->toArray();
    }

    /**
     * Only keep fields relevant for business cardholder.
     */
    private function filterBusinessCardholderData(array $validated): array
    {
        return collect($validated)->only([
            'user_id', 'card_type', 'businesses_id', 'status',
        ])->toArray();
    }
    // endregion
}
