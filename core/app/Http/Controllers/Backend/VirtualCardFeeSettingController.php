<?php

namespace App\Http\Controllers\Backend;

use App\Constants\FixPctType;
use App\Enums\VirtualCard\VirtualCardFeeOperation;
use App\Models\Currency;
use App\Models\VirtualCardFeeSetting;
use App\Models\VirtualCardProvider;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class VirtualCardFeeSettingController extends BaseController
{
    public static function permissions(): array
    {
        return [
            '*' => 'virtual-card-action',
        ];
    }

    public function index()
    {
        $feeSettings = VirtualCardFeeSetting::with(['provider', 'currency'])->paginate(20);
        $providers   = VirtualCardProvider::where('status', true)->get();
        $currencies  = Currency::where('status', true)->get();

        return view('backend.virtual_card.fee_settings.index', compact('feeSettings', 'providers', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id'        => 'required|exists:virtual_card_providers,id',
            'currency_id'        => 'required|exists:currencies,id',
            'operation'          => ['required', new Enum(VirtualCardFeeOperation::class)],
            'fee_amount'         => 'required|numeric|min:0',
            'fee_type'           => 'required|in:'.implode(',', array_keys(FixPctType::getTypeWithSymbol())),
            'min_amount'         => 'required|numeric|min:0',
            'max_amount'         => 'nullable|numeric|gt:min_amount',
            'approval_threshold' => 'nullable|numeric|min:0',
            'active'             => 'nullable|boolean',
        ]);

        // Unique fee setting validation
        if (VirtualCardFeeSetting::where('provider_id', $validated['provider_id'])
            ->where('currency_id', $validated['currency_id'])
            ->where('operation', $validated['operation'])
            ->exists()) {
            return back()->withErrors(['provider_id' => __('A fee setting for this provider, currency, and operation already exists.')])->withInput();
        }

        VirtualCardFeeSetting::create([
            'provider_id'        => $validated['provider_id'],
            'currency_id'        => $validated['currency_id'],
            'operation'          => $validated['operation'],
            'fee_amount'         => $validated['fee_amount'],
            'fee_type'           => $validated['fee_type'],
            'min_amount'         => $validated['min_amount'],
            'max_amount'         => $validated['max_amount'],
            'approval_threshold' => $validated['approval_threshold'] ?? null,
            'active'             => $request->boolean('active', true),
        ]);

        notifyEvs('success', 'Fee setting created.');

        return redirect()->route('admin.virtual-card.fee-settings.index');
    }

    public function edit($id)
    {
        $feeSetting = VirtualCardFeeSetting::findOrFail($id);
        $providers  = VirtualCardProvider::where('status', true)->get();
        $currencies = Currency::where('status', true)->get();

        return view('backend.virtual_card.fee_settings.partials._form', compact('feeSetting', 'providers', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $feeSetting = VirtualCardFeeSetting::findOrFail($id);
        $validated  = $request->validate([
            'provider_id'        => 'required|exists:virtual_card_providers,id',
            'currency_id'        => 'required|exists:currencies,id',
            'operation'          => ['required', new Enum(VirtualCardFeeOperation::class)],
            'fee_amount'         => 'required|numeric|min:0',
            'fee_type'           => 'required|in:'.implode(',', array_keys(FixPctType::getTypeWithSymbol())),
            'min_amount'         => 'required|numeric|min:0',
            'max_amount'         => 'nullable|numeric|gt:min_amount',
            'approval_threshold' => 'nullable|numeric|min:0',
            'active'             => 'nullable|boolean',
        ]);

        // Unique fee setting validation (ignore current record)
        if (VirtualCardFeeSetting::where('provider_id', $validated['provider_id'])
            ->where('currency_id', $validated['currency_id'])
            ->where('operation', $validated['operation'])
            ->where('id', '!=', $feeSetting->id)
            ->exists()) {
            return back()->withErrors(['provider_id' => __('A fee setting for this provider, currency, and operation already exists.')])->withInput();
        }

        $feeSetting->update([
            'provider_id'        => $validated['provider_id'],
            'currency_id'        => $validated['currency_id'],
            'operation'          => $validated['operation'],
            'fee_amount'         => $validated['fee_amount'],
            'fee_type'           => $validated['fee_type'],
            'min_amount'         => $validated['min_amount'],
            'max_amount'         => $validated['max_amount'],
            'approval_threshold' => $validated['approval_threshold'] ?? null,
            'active'             => $request->boolean('active', true),
        ]);

        notifyEvs('success', 'Fee setting updated.');

        return redirect()->route('admin.virtual-card.fee-settings.index');
    }

    public function destroy($id)
    {
        $feeSetting = VirtualCardFeeSetting::findOrFail($id);
        $feeSetting->delete();
        notifyEvs('success', 'Fee setting deleted.');

        return redirect()->route('admin.virtual-card.fee-settings.index');
    }
}
