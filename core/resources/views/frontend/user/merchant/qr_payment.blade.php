@extends('frontend.layouts.user.index')

@section('title', __('Generate QR Payment Link'))

@section('content')
	<div class="row">
		{{-- Form Section --}}
		<div class="col-lg-12 col-xl-7">
			<div class="single-form-card">
				{{-- Header --}}
				<div class="card-title mb-0 d-flex flex-column flex-md-row justify-content-between">
					<h6 class="text-white mb-2 mb-md-0">{{ __('Generate QR Payment Link') }}</h6>
					<div class="d-flex gap-2 flex-row">
						<a class="btn btn-light-primary btn-sm" href="{{ route('user.merchant.qr-history') }}"><i class="fas fa-list"></i>{{ __('QR Payment History') }}</a>
					</div>
				</div>
				
				{{-- Form Body --}}
				<div class="card-main">
					<form action="{{ route('user.merchant.qr-generate', $merchant->id) }}" method="POST" id="qrGenerateForm">
						@csrf
						
						<input type="hidden" name="currency" value="{{ $merchant->currency->code }}">
						<input type="hidden" id="merchantFee" value="{{ $merchant->fee }}">
						
						{{-- Amount Field --}}
						<div class="single-input-inner style-border mb-3">
							<label class="form-label">{{ __('Amount to Receive') }}</label>
							<div class="input-group input-group-right">
								<input
									type="text"
									class="form-control"
									name="amount"
									id="amountInput"
									oninput="this.value = validateDouble(this.value)"
									placeholder="{{ __('Enter the amount (e.g. 100.00)') }}"
								>
								<span class="input-group-text input-group-text-right">{{ $merchant->currency->code }}</span>
							</div>
							<span class="small text-muted d-block mt-1">{{ __('Service Fee: :fee%', ['fee' => $merchant->fee]) }}</span>
							<span class="small color-base fw-500 span-consistent"></span>
						</div>
						
						<div class="single-input-inner style-border mb-3">
							<label class="form-label">{{ __('Expiration Time (optional)') }}</label>
							<div class="input-group input-group-right">
								<input
									type="text"
									class="form-control"
									name="expire_time"
									id="expireTimeInput"
									oninput="this.value = validateNumber(this.value)"
									placeholder="{{ __('Enter expiration time in minutes') }}"
									value="30"
								>
								<span class="input-group-text input-group-text-right">{{ __('Minutes') }}</span>
							</div>
							<span class="small color-base fw-500 span-consistent">
								{{ __('Leave blank or enter 0 for no expiration. Default is 30 minutes.') }}
							</span>
						</div>
						
						<div class="single-input-inner style-border">
							<label class="form-label">{{ __('Note (Optional)') }}</label>
							<textarea class="rounded" name="note" id="noteInput" cols="10" rows="4" placeholder="{{ __('Add payment description or note...') }}"></textarea>
						</div>
						
						{{-- Submit Button --}}
						<button type="submit" class="btn btn-primary w-100" id="generateBtn" disabled>
							<i class="fas fa-qrcode me-1"></i> {{ __('Generate QR Code') }}
						</button>
					</form>
				</div>
			</div>
		</div>
		
		{{-- QR Preview Section --}}
		<div class="col-lg-12 col-xl-5">
			<div class="single-form-card d-flex flex-column">
				{{-- Card Header --}}
				<div class="card-title mb-0 d-flex justify-content-between align-items-center">
					<h6 class="text-white mt-2" id="previewTitle">{{ __('Payment Invoice Preview') }}</h6>
				</div>
				
				@if(!empty($qrCode) && !empty($paymentUrl))
					{{-- Generated QR Code Display --}}
					<div class="qr-invoice-container">
						{{-- QR Print Area --}}
						<div id="qrPrintArea" class="qr-print-section">
							{{-- Print Header --}}
							<div class="print-header text-center mb-4">
								<div class="business-info">
									<h4 class="business-name mb-1">{{ $merchant->business_name }}</h4>
									<p class="business-subtitle text-muted mb-0">{{ __('QR Payment Request') }}</p>
								</div>
								<hr class="my-3">
							</div>
							
							{{-- QR Code --}}
							<div class="qr-code-wrapper text-center mb-4">
								{!! $qrCode !!}
							</div>
							
							{{-- Payment Details --}}
							<div class="payment-details">
								<div class="detail-row">
									<span class="detail-label">{{ __('Amount:') }}</span>
									<span class="detail-value text-success fw-bold">{{ $paymentAmount ?? '' }}</span>
								</div>
								@if(isset($transaction))
									<div class="detail-row">
										<span class="detail-label">{{ __('Transaction ID:') }}</span>
										<span class="detail-value">{{ $transaction->trx_id }}</span>
									</div>
									@if($transaction->expires_at)
										<div class="detail-row">
											<span class="detail-label">{{ __('Expires At:') }}</span>
											<span class="detail-value text-warning">{{ $transaction->expires_at->format('M d, Y H:i') }}</span>
										</div>
									@endif
									@if(isset($transaction) && $transaction->note)
										<div class="detail-row">
											<span class="detail-label">{{ __('Note:') }}</span>
											<span class="detail-value">{{ $transaction->note }}</span>
										</div>
									@endif
								@endif
								<div class="detail-row">
									<span class="detail-label">{{ __('Generated:') }}</span>
									<span class="detail-value">{{ now()->format('M d, Y H:i') }}</span>
								</div>
							</div>
							
							{{-- Print Footer --}}
							<div class="print-footer mt-4 text-center">
								<div class="scan-instruction">
									<i class="fas fa-mobile-alt me-2"></i>
									{{ __('Scan this QR code with your mobile app to make payment') }}
								</div>
								<div class="security-note mt-2">
									<i class="fas fa-shield-alt me-1"></i>
									{{ __('Secure payment powered by') }} {{ config('app.name') }}
								</div>
							</div>
						</div>
						
						{{-- Action Buttons --}}
						<div class="action-buttons">
							<div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
								{{-- Copy Button --}}
								<button class="btn btn-primary btn-sm flex-grow-1 copyNow"
								        data-clipboard-text="{{ $paymentUrl }}"
								        title="{{ __('Copy Payment Link') }}"
								        data-bs-toggle="tooltip"
								        data-bs-placement="top">
									<i class="fas fa-copy me-1"></i> {{ __('Copy Link') }}
								</button>
								
								{{-- Print QR Button --}}
								<button class="btn btn-secondary btn-sm flex-grow-1" onclick="printQrCode()">
									<i class="fas fa-print me-1"></i> {{ __('Print QR') }}
								</button>
								
								{{-- Share Button --}}
								<button class="btn btn-info btn-sm flex-grow-1" onclick="sharePaymentLink()">
									<i class="fas fa-share-alt me-1"></i> {{ __('Share') }}
								</button>
							</div>
						</div>
					</div>
				@else
					{{-- Preview Mode --}}
					<div id="previewContainer" class="preview-container">
						{{-- Invoice Preview --}}
						<div class="invoice-preview border rounded p-4 bg-light">
							{{-- Business Header --}}
							<div class="business-header text-center mb-4">
								<h5 class="business-name mb-1">{{ $merchant->business_name }}</h5>
								<p class="business-subtitle text-muted mb-0">{{ __('Payment Invoice') }}</p>
							</div>
							
							{{-- QR Placeholder --}}
							<div class="qr-placeholder text-center mb-4">
								<div class="qr-placeholder-box border-2 border-dashed rounded p-4">
									<i class="fas fa-qrcode fa-4x text-muted mb-2"></i>
									<div class="placeholder-text text-muted">{{ __('QR Code will appear here') }}</div>
								</div>
							</div>
							
							{{-- Amount Preview --}}
							<div class="amount-preview">
								<div class="preview-row d-flex justify-content-between">
									<span class="preview-label">{{ __('Customer Pays:') }}</span>
									<span class="preview-value" id="customerAmount">{{ $merchant->currency->code }} 0.00</span>
								</div>
								<div class="preview-row d-flex justify-content-between">
									<span class="preview-label">{{ __('Service Fee:') }}</span>
									<span class="preview-value text-warning" id="feeAmount">{{ $merchant->currency->code }} 0.00</span>
								</div>
								<hr class="my-2">
								<div class="preview-row d-flex justify-content-between fw-bold">
									<span class="preview-label">{{ __('You Receive:') }}</span>
									<span class="preview-value text-success" id="netAmount">{{ $merchant->currency->code }} 0.00</span>
								</div>
							</div>
							
							{{-- Additional Info --}}
							<div class="additional-info mt-3">
								<div class="info-row">
									<small class="text-muted">
										<i class="fas fa-clock me-1"></i>
										{{ __('Expires in:') }} <span id="expiryPreview">30 {{ __('minutes') }}</span>
									</small>
								</div>
								<div class="info-row mt-1" id="notePreview" style="display: none;">
									<small class="text-muted">
										<i class="fas fa-sticky-note me-1"></i>
										{{ __('Note:') }} <span id="noteText"></span>
									</small>
								</div>
							</div>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection

@push('scripts')
<script>
"use strict";

$(document).ready(function() {
    const merchantFee = parseFloat($('#merchantFee').val()) || 0;
    const currencyCode = '{{ $merchant->currency->code }}';
    
    // Real-time amount calculation
    $('#amountInput').on('input keyup', function() {
        calculateAndUpdatePreview();
    });
    
    // Real-time expiry update
    $('#expireTimeInput').on('input keyup', function() {
        updateExpiryPreview();
    });
    
    // Real-time note update
    $('#noteInput').on('input keyup', function() {
        updateNotePreview();
    });
    
    function calculateAndUpdatePreview() {
        const amount = parseFloat($('#amountInput').val()) || 0;
        
        if (amount > 0) {
            const fee = (amount * merchantFee) / 100;
            const netAmount = amount - fee;
            
            // Update preview values
            $('#customerAmount').text(currencyCode + ' ' + amount.toFixed(2));
            $('#feeAmount').text(currencyCode + ' ' + fee.toFixed(2));
            $('#netAmount').text(currencyCode + ' ' + netAmount.toFixed(2));
            
            // Enable generate button
            $('#generateBtn').prop('disabled', false);
            
            // Update preview title
            $('#previewTitle').text('{{ __("Payment Invoice Preview") }} - ' + currencyCode + ' ' + amount.toFixed(2));
        } else {
            // Reset preview
            $('#customerAmount').text(currencyCode + ' 0.00');
            $('#feeAmount').text(currencyCode + ' 0.00');
            $('#netAmount').text(currencyCode + ' 0.00');
            
            // Disable generate button
            $('#generateBtn').prop('disabled', true);
            
            // Reset preview title
            $('#previewTitle').text('{{ __("Payment Invoice Preview") }}');
        }
    }
    
    function updateExpiryPreview() {
        const expireTime = parseInt($('#expireTimeInput').val()) || 30;
        if (expireTime > 0) {
            $('#expiryPreview').text(expireTime + ' {{ __("minutes") }}');
        } else {
            $('#expiryPreview').text('{{ __("No expiration") }}');
        }
    }
    
    function updateNotePreview() {
        const note = $('#noteInput').val().trim();
        if (note) {
            $('#noteText').text(note);
            $('#notePreview').show();
        } else {
            $('#notePreview').hide();
        }
    }
    
    // Initialize preview
    calculateAndUpdatePreview();
    updateExpiryPreview();
});

// Print QR Code function
function printQrCode() {
    const printContent = document.getElementById('qrPrintArea').innerHTML;
    const originalContent = document.body.innerHTML;
    
    // Create print styles
    const printStyles = `
        <style>
            @media print {
                body { 
                    font-family: 'Segoe UI', Arial, sans-serif; 
                    margin: 24px;
                    color: #222;
                    background: #fff;
                }
                .print-header {
                    padding-bottom: 12px;
                    margin-bottom: 18px;
                }
                .business-name {
                    font-size: 27px;
                    font-weight: 700;
                    margin-bottom: 3px;
                    color: #111;
                    letter-spacing: 0.2px;
                }
                .business-subtitle {
                    font-size: 13px;
                    color: #888;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .qr-code-wrapper {
                    margin: 24px 0 22px 0;
                    background: #fafbfc;
                    border-radius: 8px;
                    padding: 18px 0;
                }
                .qr-code-wrapper svg {
                    max-width: 180px;
                    height: auto;
                }
                .payment-details {
                    margin: 18px 0;
                    background: #f8f9fa;
                    border: 2px dotted #888;
                    border-radius: 8px;
                    padding: 18px 22px;
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 10px;
                    padding: 6px 0;
                    border-bottom: 1px dotted #bbb;
                }
                .detail-row:last-child {
                    margin-bottom: 0;
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: 600;
                    color: #333;
                    font-size: 14px;
                    text-transform: uppercase;
                }
                .detail-value {
                    font-weight: 700;
                    color: #000;
                    font-size: 15px;
                }
                .print-footer {
                    padding-top: 18px;
                    margin-top: 32px;
                    text-align: center;
                    border-top: 2px solid #222;
                }
                .scan-instruction {
                    font-size: 15px;
                    margin-bottom: 8px;
                    font-weight: 600;
                    color: #222;
                }
                .security-note {
                    font-size: 12px;
                    color: #888;
                    font-style: italic;
                }
            }
        </style>
    `;
    
    document.body.innerHTML = printStyles + '<div>' + printContent + '</div>';
    window.print();
    document.body.innerHTML = originalContent;
    location.reload(); // Reload to restore functionality
}

// Share payment link function
function sharePaymentLink() {
    const paymentUrl = $('.copyNow').data('clipboard-text');
    
    if (navigator.share) {
        navigator.share({
            title: '{{ __("Payment Request") }}',
            text: '{{ __("Please complete your payment using this link") }}',
            url: paymentUrl
        }).catch(console.error);
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(paymentUrl).then(function() {
            notifyEvs('success', '{{ __("Payment link copied to clipboard") }}');
        });
    }
}
</script>
@endpush
