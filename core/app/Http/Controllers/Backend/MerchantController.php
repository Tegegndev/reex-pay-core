<?php

namespace App\Http\Controllers\Backend;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use App\Notifications\TemplateNotification;
use Illuminate\Http\Request;

class MerchantController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index|pendingMerchant|approvedMerchant|rejectedMerchant' => 'merchant-list',
            'merchantAction'                                          => 'merchant-manage',
        ];
    }

    public function index(Request $request)
    {
        $title     = __('Merchant List');
        $merchants = Merchant::query()
            ->filter($request)
            ->paginate(10)
            ->withQueryString();

        return view('backend.merchant.index', compact('merchants', 'title'));
    }

    public function pendingMerchant(Request $request)
    {
        $title     = __('Pending Merchant');
        $merchants = Merchant::query()
            ->where('status', MerchantStatus::PENDING)
            ->filter($request)
            ->paginate(10)
            ->withQueryString();

        return view('backend.merchant.index', compact('merchants', 'title'));
    }

    public function approvedMerchant(Request $request)
    {
        $title     = __('Approved Merchant');
        $merchants = Merchant::query()
            ->where('status', MerchantStatus::APPROVED)
            ->filter($request)
            ->paginate(10)
            ->withQueryString();

        return view('backend.merchant.index', compact('merchants', 'title'));
    }

    public function rejectedMerchant(Request $request)
    {
        $title     = __('Rejected Merchant');
        $merchants = Merchant::query()
            ->where('status', MerchantStatus::REJECTED)
            ->filter($request)
            ->paginate(10)
            ->withQueryString();

        return view('backend.merchant.index', compact('merchants', 'title'));
    }

    public function merchantAction(Request $request)
    {
        $validated = $request->validate([
            'merchant_id'      => 'required|exists:merchants,id',
            'fee'              => 'required_if:action,==,approve|nullable|numeric',
            'action'           => 'required|in:approve,reject',
            'rejection_reason' => 'nullable',
        ]);

        $merchant = Merchant::findOrFail($validated['merchant_id']);

        $updateData        = ['status' => $validated['action'] === 'approve' ? MerchantStatus::APPROVED : MerchantStatus::REJECTED];
        $updateData['fee'] = $validated['fee'] ?? 0;

        if ($merchant->status === MerchantStatus::PENDING) {
            if ($validated['action'] === 'approve') {
                $merchant->user->notify(new TemplateNotification(
                    identifier: 'merchant_user_notify_shop_approved',
                    data: [
                        'business_name' => $merchant->business_name,
                    ],
                    action: route('user.merchant.index'),
                ));
            } else {
                $merchant->user->notify(new TemplateNotification(
                    identifier: 'merchant_user_notify_shop_rejected',
                    data: [
                        'business_name'    => $merchant->business_name,
                        'rejection_reason' => $validated['rejection_reason'] ?? __('No reason provided'),
                    ],
                    action: route('user.merchant.index'),
                ));
            }
        }

        $merchant->update($updateData);

        notifyEvs('success', 'Merchant  updated successfully');

        return back();
    }
}
