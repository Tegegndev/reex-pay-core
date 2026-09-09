<?php

namespace App\Http\Controllers\Backend;

use App\Enums\KycStatus;
use App\Exceptions\NotifyErrorException;
use App\Models\KycSubmission;
use App\Notifications\TemplateNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class KycController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'pending|index' => 'kyc-list',
            'requestAction' => 'kyc-action',
        ];
    }

    public function pending(Request $request)
    {
        $kycPendingRequests = KycSubmission::where('status', KycStatus::PENDING)
            ->filter($request)
            ->latest() // or ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('backend.kyc.pending', compact('kycPendingRequests'));
    }

    public function index(Request $request)
    {
        $kycRequests = KycSubmission::filter($request)->latest()->paginate(10)->withQueryString();

        return view('backend.kyc.index', compact('kycRequests'));
    }

    /**
     * Handle admin action to approve or reject a KYC submission.
     *
     * @return RedirectResponse
     *
     * @throws NotifyErrorException|Throwable
     */
    public function requestAction(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'submission_id' => 'required|integer|exists:kyc_submissions,id',
            'action'        => 'required|in:approve,reject',
            'remarks'       => 'nullable|string',
        ]);

        // Retrieve the submission to be processed
        $submission = KycSubmission::findOrFail($validated['submission_id']);

        // Ensure the submission is still pending
        if ($submission->status !== KycStatus::PENDING) {
            throw new NotifyErrorException(__('KYC submission is already processed.'));
        }

        try {
            // Use a transaction to safely update the submission status
            DB::transaction(function () use ($submission, $validated) {
                $submission->status = $validated['action'] === 'approve' ? KycStatus::APPROVED : KycStatus::REJECTED;
                $submission->notes  = $validated['remarks'] ?? null;
                $submission->save();

                // Notify the user
                if ($validated['action'] === 'approve') {
                    $submission->user->notify(new TemplateNotification(
                        identifier: 'kyc_user_notify_approved',
                        data: [
                            'kyc_type' => $submission->kycTemplate->title,
                        ],
                        action: route('user.settings.kyc.verify'),
                    ));
                } else {
                    $submission->user->notify(new TemplateNotification(
                        identifier: 'kyc_user_notify_rejected',
                        data: [
                            'kyc_type'         => $submission->kycTemplate->title,
                            'rejection_reason' => $validated['remarks'],
                        ],
                    ));
                }

            });

            notifyEvs('success', __('KYC submission updated successfully.'));

            return redirect()->back();
        } catch (Throwable $e) {
            Log::error('Error updating KYC submission: '.$e->getMessage());
            DB::rollBack();
            throw new NotifyErrorException(__('Failed to update KYC submission: ').$e->getMessage());
        }
    }
}
