<?php

namespace App\Http\Controllers\Backend;

use App\Models\PaymentGateway;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;

class PaymentGatewayController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index'       => 'payment-gateway-list',
            'edit|update' => 'payment-gateway-configure',
        ];
    }

    public function index()
    {
        $paymentGateways = PaymentGateway::paginate(10);

        return view('backend.payment_gateway.index', compact('paymentGateways'));
    }

    public function edit($id)
    {
        $paymentGateway = PaymentGateway::getById($id);

        return view('backend.payment_gateway.edit', compact('paymentGateway'))->render();
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required',
            'credentials' => 'required',
            'status'      => 'boolean',
        ]);

        $paymentGateway = PaymentGateway::with([
            'depositMethods',
            'withdrawMethods',
        ])->find($id);

        $validated['status'] = $request->boolean('status');

        if (! $validated['status']) {
            $paymentGateway->depositMethods()->update([
                'status' => false,
            ]);

            $paymentGateway->withdrawMethods()->update([
                'status' => false,
            ]);
        }

        $paymentGateway->update($validated);

        if ($validated['status']) {
            $paymentGateway->depositMethods()->update([
                'status' => true,
            ]);

            $paymentGateway->withdrawMethods()->update([
                'status' => true,
            ]);
        }

        notifyEvs(
            'success',
            __('Payment Gateway Updated Successfully')
        );

        return redirect()->back();
    }

    public function gatewayCurrency($gateway_id)
    {
        $paymentGateway = PaymentGateway::getById($gateway_id);

        $supportedCurrencies = $paymentGateway->currencies;

        return [
            'view' => view(
                'backend.payment_gateway.partial._currencies_list',
                compact('supportedCurrencies')
            )->render(),
        ];
    }
}