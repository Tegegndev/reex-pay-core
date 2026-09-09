<?php

namespace App\Http\Controllers\Backend;

use App\Enums\MethodType;
use App\Http\Requests\WithdrawMethod\StoreUpdateRequest;
use App\Models\PaymentGateway;
use App\Models\WithdrawMethod;
use App\Traits\FileManageTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class WithdrawMethodController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index'             => 'withdraw-method-list',
            'store|edit|update' => 'withdraw-method-manage',
        ];
    }

    /**
     * Display a listing of withdraw methods.
     *
     * @return View
     */
    public function index()
    {
        // Get the type of withdraw method from the request
        $type = MethodType::tryFrom(request('type'));

        // Retrieve all payment gateways
        $paymentGateways = PaymentGateway::withdrawAvailable()->get();
        // Retrieve withdraw methods of the specified type, paginated
        $paymentMethods = WithdrawMethod::where('type', $type)->paginate(10);

        // Return the view with the required data
        return view('backend.withdraw.method.index', compact('type', 'paymentGateways', 'paymentMethods'));
    }

    /**
     * Stores a new withdraw method.
     *
     * @param  StoreUpdateRequest $request The request containing the withdraw method data.
     * @return RedirectResponse   A redirect response to the withdraw method index page.
     */
    public function store(StoreUpdateRequest $request)
    {
        // Prepare the data for creating a new withdraw method.
        $data = $this->prepareData($request);

        // Create a new withdrawal method using the prepared data.
        WithdrawMethod::create($data);

        // Notify the user of a successful withdrawal method creation.
        notifyEvs('success', __('Withdraw Method Added Successfully'));

        // Redirect the user back to the withdrawal method index page.
        return redirect()->route('admin.withdraw.method.index', ['type' => $request->input('type')]);
    }

    /**
     * Prepares the data for creating or updating a withdrawMethod.
     *
     * @return array
     */
    private function prepareData(StoreUpdateRequest $request, ?WithdrawMethod $paymentMethod = null)
    {
        // Get the validated request data
        $validated = $request->validated();

        // Determine the payment gateway, safely handling null values
        $paymentGateway = $request->filled('payment_gateway_id')
            ? PaymentGateway::find($request->input('payment_gateway_id'))
            : ($paymentMethod?->paymentGateway ?? null);

        // Get withdraw field from the payment gateway (if available)
        $withdrawField = $paymentGateway?->is_withdraw ?? 'default_field_name'; // Fallback field name

        // Generate method_code if payment gateway and currency are available
        $method_code = ($paymentGateway && $request->filled('currency'))
            ? "{$paymentGateway->code}-".strtolower($request->input('currency'))
            : ($validated['method_code'] ?? 'default-method-code'); // Fallback method_code

        // Handle logo upload safely (fallback to null if nothing exists)
        $logo = $request->hasFile('logo')
            ? self::uploadImage($request->file('logo'), $paymentMethod?->logo)
            : ($paymentMethod?->logo ?? null);

        // Prepare withdraw fields safely
        $fields = $request->filled('fields')
            ? $request->input('fields')
            : [['name' => $withdrawField, 'type' => 'text', 'validation' => 'required']];

        // Handle charge fields - provide defaults for backward compatibility
        $chargeData = [
            // New charge fields for user and merchant
            'user_charge'          => $request->input('user_charge', 0),
            'user_charge_type'     => $request->input('user_charge_type', 'percent'),
            'merchant_charge'      => $request->input('merchant_charge', 0),
            'merchant_charge_type' => $request->input('merchant_charge_type', 'percent'),

            // Old charge fields - provide defaults to avoid errors
            'charge'      => $request->input('user_charge', 0), // Default to user_charge for backward compatibility
            'charge_type' => $request->input('user_charge_type', 'percent'), // Default to user_charge_type
        ];

        // Return the merged array
        return array_merge($validated, compact('logo', 'method_code', 'fields'), $chargeData, [
            'status' => $request->boolean('status'),
        ]);
    }

    /**
     * Displays the edit form for a withdraw method.
     *
     * @param  int    $id The ID of the withdraw method to edit.
     * @return string The rendered edit form view.
     *
     * @throws Throwable
     */
    public function edit(int $id)
    {
        // Retrieve the withdraw method with its associated payment gateway.
        $paymentMethod = WithdrawMethod::with('paymentGateway')->find($id);

        // Get all available payment gateways.
        $paymentGateways = PaymentGateway::active()->get();

        // Render the edit form view with the retrieved data.
        return view('backend.withdraw.method.partials._edit_payment_method_form_data', compact('paymentMethod', 'paymentGateways'))->render();
    }

    /**
     * Updates a withdraw method.
     *
     * @param  int              $id
     * @return RedirectResponse
     */
    public function update(StoreUpdateRequest $request, $id)
    {

        // Retrieve the withdraw method by its ID
        $paymentMethod = WithdrawMethod::find($id);

        // Prepare the data for updating the withdraw method
        $data = $this->prepareData($request, $paymentMethod);

        // Update the withdraw method with the prepared data
        $paymentMethod->update($data);

        // Notify the user of a successful update
        notifyEvs('success', __('Withdraw Method Updated Successfully'));

        // Redirect the user back to the withdrawal method index page
        return redirect()->route('admin.withdraw.method.index', ['type' => $request->input('type')]);
    }
}
