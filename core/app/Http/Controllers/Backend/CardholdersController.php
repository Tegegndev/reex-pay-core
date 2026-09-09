<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\VirtualCard\CardholderStatus;
use App\Models\Cardholders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CardholdersController extends BaseController
{
    public static function permissions(): array
    {
        return [
            '*' => 'virtual-card-action',
        ];
    }

    /**
     * Show all cardholders (personal & business).
     */
    public function index(Request $request)
    {
        $cardholders = Cardholders::with(['user', 'business'])
            ->status($request->input('status'))
            ->search($request->input('search'))
            ->orderByDesc('created_at')
            ->paginate(20);

        $statuses = CardholderStatus::options();

        return view('backend.virtual_card.cardholder.index', compact('cardholders', 'statuses'));
    }

    /**
     * Handle approve or reject cardholder action (personal & business).
     */
    public function action(Request $request, $id)
    {
        $cardholder = Cardholders::findOrFail($id);
        if ($cardholder->status !== CardholderStatus::PENDING) {
            return Redirect::back()->with('error', __('Already processed.'));
        }
        $action = $request->input('action');
        if ($action === 'approve') {
            DB::transaction(function () use ($cardholder) {
                $cardholder->status = CardholderStatus::APPROVED;
                $cardholder->save();
                Log::info('Cardholder approved', ['id' => $cardholder->id]);
            });
            notifyEvs('success', __('Cardholder approved.'));

            return Redirect::back()->with('success', __('Cardholder approved.'));
        } elseif ($action === 'reject') {
            DB::transaction(function () use ($cardholder) {
                $cardholder->status = CardholderStatus::REJECTED;
                $cardholder->save();
                Log::info('Cardholder rejected', ['id' => $cardholder->id]);
            });
            notifyEvs('success', __('Cardholder rejected.'));

            return Redirect::back()->with('success', __('Cardholder rejected.'));
        }
        notifyEvs('error', __('Invalid action.'));

        return Redirect::back()->with('error', __('Invalid action.'));
    }
}
