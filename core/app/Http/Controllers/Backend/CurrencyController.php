<?php

namespace App\Http\Controllers\Backend;

use App\Constants\CurrencyRole;
use App\Constants\CurrencyType;
use App\Models\Currency;
use App\Traits\FileManageTrait;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;

class CurrencyController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index|store|edit|update|destroy' => 'currency-manage',
        ];
    }

    // Display a listing of currencies
    public function index()
    {
        $currencies = Currency::with(['activeRoles'])->get();

        return view('backend.currencies.index', compact('currencies'));
    }

    // Store a newly created currency in storage
    public function store(Request $request)
    {
        // Validate and prepare currency data
        $validated                = $this->validateCurrencyData($request);
        $validated['auto_wallet'] = $this->toBoolean($request->default ? true : $request->auto_wallet);
        $validated['status']      = $this->toBoolean($request->status);
        $validated['default']     = $this->toBoolean($request->default);

        // Ensure only one default currency exists
        if ($validated['default']) {
            Currency::where('default', true)->update(['default' => false]);
        } else {
            $this->enforceDefaultCurrency();
        }

        // Process and assign the flag image, if present
        $validated['flag'] = $this->handleFlagUpload($request);

        // Create currency record and notify user
        $currency = Currency::create($validated);

        // Get roles data from the request
        $rolesData = $request->input('roles', CurrencyRole::getRoles());

        // Loop through the roles and update them
        foreach ($rolesData as $role) {
            // Find the role by its ID, and if it exists, update it
            $currency->roles()->create([
                'role_name' => $role['role_name'],
                'fee'       => $role['fee']       ?? 0,
                'fee_type'  => $role['fee_type']  ?? null,
                'min_limit' => $role['min_limit'] ?? 0,
                'max_limit' => $role['max_limit'] ?? null,
                'is_active' => $role['status']    ?? false,
            ]);
        }

        notifyEvs('success', __('Currency created successfully'));

        return redirect()->back();
    }

    // Remove the specified currency from storage

    public function update(Request $request, Currency $currency)
    {

        // Validate and prepare currency data
        $validated                = $this->validateCurrencyData($request, true);
        $validated['auto_wallet'] = $this->toBoolean($request->default ? true : $request->auto_wallet);
        $validated['status']      = $this->toBoolean($request->status);
        $validated['default']     = $this->toBoolean($request->default);

        // Prevent disabling the default currency
        if (($currency->default || $validated['default']) && ! $validated['status']) {
            return redirect()->back()->withErrors(__('The default currency cannot be disabled.'));
        }

        // Prevent changing the default currency
        if ($currency->default && ! $validated['default']) {
            return redirect()->back()->withErrors(__('The default currency cannot be changed.'));
        }

        // Ensure only one default currency exists
        if ($validated['default'] && ! $currency->default) {
            Currency::where('default', true)->update(['default' => false]);
        } else {
            $this->enforceDefaultCurrency();
        }

        // Process and assign the flag image if present
        if ($request->hasFile('flag')) {
            // Remove the old flag image if necessary
            $validated['flag'] = $this->handleFlagUpload($request, $currency->flag);
        }

        // Update currency record and notify user
        $currency->update($validated);

        // Get roles data from the request
        $rolesData = $request->input('roles', []);

        // Loop through the roles and update them
        foreach ($rolesData as $id => $role) {
            // Find the role by its ID, and if it exists, update it
            $currency->roles()->where('id', $id)->update([
                'fee'       => $role['fee']       ?? 0,
                'fee_type'  => $role['fee_type']  ?? null,
                'min_limit' => $role['min_limit'] ?? 0,
                'max_limit' => $role['max_limit'] ?? null,
                'is_active' => $role['status']    ?? false,
            ]);
        }
        notifyEvs('success', __('Currency updated successfully'));

        return redirect()->back();
    }

    public function edit(Currency $currency)
    {
        // Eager load the 'roles' relationship along with the currency
        $currency->load('roles');

        return view('backend.currencies.edit', compact('currency'))->render();
    }

    public function destroy(Currency $currency)
    {
        // Prevent deleting the default currency
        if ($currency->default) {
            return redirect()->back()->withErrors(__('The default currency cannot be deleted.'));
        }

        // Check if there are active wallets associated with this currency
        if (DB::table('wallets')->where('currency_id', $currency->id)->exists()) {
            return redirect()->back()->withErrors(__('Cannot delete currency with active wallets.'));
        }

        try {
            // Use a database transaction for consistency
            DB::transaction(function () use ($currency) {
                // Remove the flag image if it exists
                if ($currency->flag) {
                    $this->delete($currency->flag);
                }

                // Delete the currency record
                $currency->delete();
            });

            // Success notification
            notifyEvs('success', __('Currency deleted successfully'));

            return redirect()->back();
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Currency Deletion Error: '.$e->getMessage());

            notifyEvs('error', __('An error occurred while deleting the currency.'));

            // Return with an error message
            return redirect()->back();
        }
    }

    // Show the form for editing the specified currency

    /**
     * Validate the currency data from the request.
     */
    protected function validateCurrencyData(Request $request, $isUpdate = false)
    {
        $rules = [
            'flag'          => $isUpdate ? 'sometimes' : 'required',
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:10'.($isUpdate ? '' : '|unique:currencies'),
            'symbol'        => 'required|string|max:10',
            'type'          => 'required|in:'.implode(',', CurrencyType::getTypes()),
            'exchange_rate' => 'required_if:rate_live,0',
            'rate_live'     => 'boolean',
            'roles'         => $isUpdate ? 'sometimes|array' : 'required|array',
        ];

        return $request->validate($rules);
    }

    // Update the specified currency in storage

    /**
     * Convert a value to boolean.
     */
    protected function toBoolean($value)
    {
        return (bool) $value;
    }

    /**
     * Ensure at least one currency is set as default.
     */
    protected function enforceDefaultCurrency()
    {
        if (! Currency::where('default', true)->exists()) {
            Currency::first()->update(['default' => true]);
        }
    }

    /**
     * Handle flag image upload if the file is present.
     */
    protected function handleFlagUpload(Request $request, $old = null)
    {
        return $request->hasFile('flag') ? self::uploadImage($request->file('flag'), $old) : null;
    }
}
