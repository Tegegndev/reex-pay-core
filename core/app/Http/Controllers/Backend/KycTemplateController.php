<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\NotifyErrorException;
use App\Models\KycTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class KycTemplateController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index'                     => 'kyc-template-list',
            'store|edit|update|destroy' => 'kyc-template-manage',
        ];
    }

    /**
     * Display a listing of the KYC templates.
     */
    public function index()
    {
        $templates = KycTemplate::latest()->get();

        return view('backend.kyc.template.index', compact('templates'));
    }

    /**
     * Store a newly created KYC template in storage.
     *
     * @throws NotifyErrorException|Throwable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255', 'unique:kyc_templates,title'],
            'description'   => 'nullable|string|max:1000',
            'fields'        => 'required|array',
            'applicable_to' => 'required|array',
            'status'        => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            KycTemplate::create($validated);
            DB::commit();
            notifyEvs('success', __('KYC Template created successfully!'));

            return redirect()->route('admin.kyc.template.index');
        } catch (Throwable $th) {
            DB::rollBack();
            throw new NotifyErrorException(__('Failed to process KYC Template: ').$th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified KYC template.
     */
    public function edit(KycTemplate $template)
    {
        return view('backend.kyc.template.edit', compact('template'));
    }

    /**
     * Update the specified KYC template in storage.
     *
     * @throws NotifyErrorException|Throwable
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kyc_templates', 'title')->ignore($id),
            ],
            'description'   => 'nullable|string|max:1000',
            'fields'        => 'required|array',
            'applicable_to' => 'required|array',
            'status'        => 'required|boolean',
        ]);
        DB::beginTransaction();
        try {
            $kycTemplate = KycTemplate::findOrFail($id);
            // Ensure you’re updating the existing instance
            $kycTemplate->update($validated);
            DB::commit();
            notifyEvs('success', __('KYC Template updated successfully!'));

            return redirect()->route('admin.kyc.template.index');
        } catch (Throwable $th) {
            DB::rollBack();
            throw new NotifyErrorException(__('Failed to process KYC Template: ').$th->getMessage());
        }
    }

    /**
     * Remove the specified KYC template from storage.
     *
     * @throws NotifyErrorException|Throwable
     */
    public function destroy(KycTemplate $template)
    {
        DB::beginTransaction();
        try {
            $template->delete();
            DB::commit();

            notifyEvs('success', __('KYC Template deleted successfully!'));

            return redirect()->route('admin.kyc.template.index');
        } catch (Throwable $th) {
            DB::rollBack();
            throw new NotifyErrorException(__('Failed to delete KYC Template: ').$th->getMessage());
        }
    }
}
