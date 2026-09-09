<div class="card mb-2 border-0 shadow-none">
    <div class="card-body p-0">
        <div class="row mb-2">
            <div class="col-7">
                <strong>{{ $cardDetails['card_name'] ?? 'Virtual Card' }}</strong>
                <span class="badge bg-success ms-2 text-uppercase">{{ $cardDetails['card_status'] ?? '' }}</span>
            </div>
            <div class="col-5 text-end">
                <span class="text-muted small">{{ $cardDetails['card_brand'] ?? '' }}</span>
            </div>
        </div>
        <div class="row mb-2 align-items-center">
            <div class="col-8">
                <div class="mb-1">
                    <strong>Card Number:</strong><br>
                    <span class="text-monospace fs-5">
                        {{ $cardDetails['card_number'] ?? '**** **** **** XXXX' }}
                    </span>
                </div>
            </div>
            <div class="col-2">
                <strong>CVV:</strong><br>
                <span class="text-monospace">{{ $cardDetails['cvv'] ?? '***' }}</span>
            </div>
            <div class="col-2">
                <strong>Expiry:</strong><br>
                <span class="text-monospace">{{ $cardDetails['expiry'] ?? '--/--' }}</span>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <strong>Cardholder:</strong><br>
                {{ $cardDetails['card_holder_name'] ?? '-' }}
            </div>
            <div class="col-6">
                <strong>Type:</strong><br>
                {{ $cardDetails['card_type'] ?? '-' }}
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <strong>Balance:</strong><br>
                <span class="fw-bold">${{ number_format($cardDetails['balance'] ?? 0, 2) }}</span>
            </div>
            <div class="col-6">
                <strong>Created:</strong><br>
                {{ \Illuminate\Support\Carbon::parse($cardDetails['card_created_date'] ?? $cardDetails['created_at'] ?? null)->format('Y-m-d') ?? '-' }}
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <strong>Billing Address:</strong><br>
                {{ $cardDetails['billing_street'] ?? '-' }}<br>
                {{ $cardDetails['billing_city'] ?? '-' }}, {{ $cardDetails['billing_country'] ?? '-' }}<br>
                {{ $cardDetails['billing_zip_code'] ?? '-' }}
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <strong>Email:</strong><br>
                {{ $cardDetails['customer_email'] ?? '-' }}
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-6">
                <strong>Reference:</strong><br>
                {{ $cardDetails['reference'] ?? '-' }}
            </div>
        </div>
    </div>
</div>