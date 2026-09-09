<?php

namespace App\Http\Controllers\Backend;

use App\Enums\MethodType;
use App\Http\Requests\DepositMethod\StoreUpdateRequest;
use App\Models\DepositMethod;
use App\Models\PaymentGateway;
use App\Traits\FileManageTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Mews\Purifier\Facades\Purifier;
use Throwable;

class DepositMethodController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index|'            => 'deposit-method-list',
            'store|edit|update' => 'deposit-method-manage',
        ];
    }

    /**
     * Display a listing of deposit methods.
     *
     * @return View
     */
    public function index()
    {
        // Get the type of deposit method from the request
        $type = MethodType::tryFrom(request('type'));
        // Retrieve all payment gateways
        $paymentGateways = PaymentGateway::active()->get();

        // Retrieve deposit methods of the specified type, paginated
        $paymentMethods = DepositMethod::where('type', $type)->paginate(10);

        // Return the view with the required data
        return view('backend.deposit.method.index', compact('type', 'paymentGateways', 'paymentMethods'));
    }

    /**
     * Stores a new deposit method.
     *
     * @param  StoreUpdateRequest $request The request containing the deposit method data.
     * @return RedirectResponse   A redirect response to the deposit method index page.
     */
    public function store(StoreUpdateRequest $request)
    {
        // Prepare the data for creating a new deposit method.
        $data = $this->prepareData($request);

        // Create a new deposit method using the prepared data.
        DepositMethod::create($data);

        // Notify the user of a successful deposit method creation.
        notifyEvs('success', __('Deposit Method Added Successfully'));

        // Redirect the user back to the deposit method index page.
        return redirect()->route('admin.deposit.method.index', ['type' => $request->input('type')]);
    }

    /**
     * Displays the edit form for a deposit method.
     *
     * @param  int    $id The ID of the deposit method to edit.
     * @return string The rendered edit form view.
     *
     * @throws Throwable
     */
    public function edit(int $id)
    {
        // Retrieve the deposit method with its associated payment gateway.
        $paymentMethod = DepositMethod::with('paymentGateway')->find($id);

        // Get all available payment gateways.
        $paymentGateways = PaymentGateway::active()->get();

        // Render the edit form view with the retrieved data.
        return view('backend.deposit.method.partials._edit_payment_method_form_data', compact('paymentMethod', 'paymentGateways'))->render();
    }

    /**
     * Updates a deposit method.
     *
     * @param  int              $id
     * @return RedirectResponse
     */
    public function update(StoreUpdateRequest $request, $id)
    {

        // Retrieve the deposit method by its ID
        $paymentMethod = DepositMethod::find($id);

        // Prepare the data for updating the deposit method
        $data = $this->prepareData($request, $paymentMethod);

        // Update the deposit method with the prepared data
        $paymentMethod->update($data);

        // Notify the user of a successful update
        notifyEvs('success', __('Deposit Method Updated Successfully'));

        // Redirect the user back to the deposit method index page
        return redirect()->route('admin.deposit.method.index', ['type' => $request->input('type')]);
    }

    /**
     * Prepares the data for creating or updating a DepositMethod.
     *
     * @return array
     */
    private function prepareData(StoreUpdateRequest $request, ?DepositMethod $paymentMethod = null)
    {

        // Get the validated request data
        $validated = $request->validated();

        // Generate the method code if payment gateway ID and currency are present
        $methodCode = $request->input('payment_gateway_id')
            ? PaymentGateway::find($request->input('payment_gateway_id'))->code.'-'.strtolower($request->input('currency'))
            : $validated['method_code'];

        // Merge the validated data with additional fields
        return array_merge($validated, [
            // Upload a new logo if present, otherwise use the existing logo
            'logo'        => $request->hasFile('logo') ? self::uploadImage($request->file('logo'), $paymentMethod?->logo) : ($paymentMethod?->logo),
            'method_code' => $methodCode,
            'fields'      => $request->input('fields'),
            // Clean and decode the receive payment details if present
            'receive_payment_details' => $request->filled('receive_payment_details') ? Purifier::clean(htmlspecialchars_decode($request->input('receive_payment_details'))) : null,
            // Get the status as a boolean
            'status' => $request->boolean('status'),
            // Provide defaults for legacy charge fields to avoid validation errors
            'charge'      => $validated['charge']           ?? $validated['user_charge'] ?? 0,
            'charge_type' => $validated['charge_type'] ?? $validated['user_charge_type'] ?? 'percent',
        ]);
    }
}
