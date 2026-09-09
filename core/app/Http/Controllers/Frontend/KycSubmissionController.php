<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\KycStatus;
use App\Exceptions\NotifyErrorException;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\KycSubmission;
use App\Models\KycTemplate;
use App\Notifications\TemplateNotification;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class KycSubmissionController extends Controller
{
    use FileManageTrait;

    public function kycVerify()
    {
        $kycTemplates = KycTemplate::where('status', true)
            ->whereJsonContains('applicable_to', auth()->user()->role->value)
            ->get();

        return view('frontend.user.setting.kyc_verify', compact('kycTemplates'));
    }

    public function templateDetails($id)
    {
        $template = KycTemplate::findOrFail($id);

        return view('frontend.user.setting.partials._kyc_template_details', compact('template'))->render();
    }

    /**
     * @throws Throwable
     * @throws NotifyErrorException
     */
    public function kycSubmit(Request $request)
    {
        // Retrieve the KYC template based on the template_id input and the user's role.
        $templateId  = $request->input('template_id');
        $note        = $request->input('note') ?? null;
        $kycTemplate = KycTemplate::active()
            ->where('id', $templateId)
            ->whereJsonContains('applicable_to', auth()->user()->role->value)
            ->firstOrFail();

        // Build dynamic validation rules based on the template fields.
        $rules = [];
        foreach ($kycTemplate->fields as $index => $field) {
            // Define the field key in the request.
            $fieldKey = "credentials.{$field['label']}";

            // Check for the 'required' flag allowing for both string 'true' and boolean true.
            $isRequired = isset($field['required']) && ($field['required'] === 'true' || $field['required'] === true);

            // Start with either a required or nullable rule.
            $rules[$fieldKey] = $isRequired ? ['required'] : ['nullable'];

            // Add additional rules based on the field type.
            if (isset($field['type']) && $field['type'] === 'file') {
                $rules[$fieldKey][] = 'file';
            } else {
                $rules[$fieldKey][] = 'string';
            }
        }

        // Validate the request with the dynamic rules.
        $validatedData = $request->validate($rules);

        DB::beginTransaction();

        try {
            $user               = auth()->user();
            $existingSubmission = $user->kycSubmission;

            // Prevent a new submission if one already exists and is not rejected.
            if ($existingSubmission && $existingSubmission->status !== KycStatus::REJECTED) {
                throw new NotifyErrorException(__('You already have an active KYC submission.'));
            }

            // Update an existing rejected submission or create a new one.
            if ($existingSubmission && $existingSubmission->status === KycStatus::REJECTED) {
                $submission = $existingSubmission;
                $submission->update([
                    'notes'  => $note,
                    'status' => KycStatus::PENDING,
                ]);
            } else {
                $submission = KycSubmission::create([
                    'kyc_template_id' => $kycTemplate->id,
                    'notes'           => $note,
                    'user_id'         => $user->id,
                    'status'          => KycStatus::PENDING,
                ]);
            }

            // Process each field's data.
            $submissionData = [];
            foreach ($kycTemplate->fields as $index => $field) {
                $value = $validatedData['credentials'][$field['label']] ?? null;

                // If the field is a file and a file was uploaded, handle the file upload.
                if (isset($field['type']) && $field['type'] === 'file' && $request->hasFile("credentials.{$field['label']}")) {
                    $file  = $request->file("credentials.{$field['label']}");
                    $value = $this->uploadFile($file);
                }
                $submissionData[$field['label']] = $value;
            }

            // Update the submission with the processed data.
            $submission->update(['submission_data' => $submissionData]);

            // Notify admin
            $admins = Admin::permission('kyc-notification')->get();
            Notification::send($admins, new TemplateNotification(
                identifier: 'kyc_admin_notify_submission',
                data: [
                    'user'     => auth()->user()->name,
                    'kyc_type' => $kycTemplate->title,
                ],
                sender: auth()->user(),
                action: route('admin.kyc.pending')
            ));

            DB::commit();

            // Notify success and redirect back.
            notifyEvs('success', __('KYC submission received successfully!'));

            return redirect()->back();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('KYC Submission error: '.$e->getMessage());
            throw new NotifyErrorException(__('Failed to submit KYC: ').$e->getMessage());
        }
    }
}
