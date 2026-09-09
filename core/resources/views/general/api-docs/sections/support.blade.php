<!-- Error Codes Section -->
<section id="error-codes" class="content-section">
    <h2>@lang('Error Codes')</h2>
    <p>@lang('REEXPAY API uses conventional HTTP response codes to indicate the success or failure of API requests.')</p>

    <!-- HTTP Status Codes -->
    <h3>@lang('HTTP Status Codes')</h3>
    <table class="api-table">
        <thead>
            <tr>
                <th>@lang('Code')</th>
                <th>@lang('Status')</th>
                <th>@lang('Description')</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="status-code status-200">200</span></td>
                <td><strong>@lang('OK')</strong></td>
                <td>@lang('Request succeeded')</td>
            </tr>
            <tr>
                <td><span class="status-code status-400">400</span></td>
                <td><strong>@lang('Bad Request')</strong></td>
                <td>@lang('Invalid request parameters')</td>
            </tr>
            <tr>
                <td><span class="status-code status-401">401</span></td>
                <td><strong>@lang('Unauthorized')</strong></td>
                <td>@lang('Invalid or missing API credentials')</td>
            </tr>
            <tr>
                <td><span class="status-code status-400">403</span></td>
                <td><strong>@lang('Forbidden')</strong></td>
                <td>@lang('Insufficient permissions')</td>
            </tr>
            <tr>
                <td><span class="status-code status-400">404</span></td>
                <td><strong>@lang('Not Found')</strong></td>
                <td>@lang('Resource not found')</td>
            </tr>
            <tr>
                <td><span class="status-code status-400">429</span></td>
                <td><strong>@lang('Too Many Requests')</strong></td>
                <td>@lang('Rate limit exceeded')</td>
            </tr>
            <tr>
                <td><span class="status-code status-500">500</span></td>
                <td><strong>@lang('Internal Server Error')</strong></td>
                <td>@lang('Server error occurred')</td>
            </tr>
        </tbody>
    </table>

    <!-- API Error Codes -->
    <h3>@lang('API Error Codes')</h3>
    <table class="api-table">
        <thead>
            <tr>
                <th>@lang('Error Code')</th>
                <th>@lang('Description')</th>
                <th>@lang('Solution')</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>INVALID_CREDENTIALS</code></td>
                <td>@lang('Invalid API credentials provided')</td>
                <td>@lang('Check your Merchant ID and API Key')</td>
            </tr>
            <tr>
                <td><code>INSUFFICIENT_FUNDS</code></td>
                <td>@lang('Customer has insufficient funds')</td>
                <td>@lang('Customer needs to add funds to their wallet')</td>
            </tr>
            <tr>
                <td><code>PAYMENT_DECLINED</code></td>
                <td>@lang('Payment was declined by payment processor')</td>
                <td>@lang('Customer should try a different payment method')</td>
            </tr>
            <tr>
                <td><code>INVALID_AMOUNT</code></td>
                <td>@lang('Payment amount is invalid')</td>
                <td>@lang('Check minimum and maximum amount limits')</td>
            </tr>
            <tr>
                <td><code>INVALID_CURRENCY</code></td>
                <td>@lang('Unsupported currency code')</td>
                <td>@lang('Use a supported currency code (USD, EUR, etc.)')</td>
            </tr>
            <tr>
                <td><code>DUPLICATE_REFERENCE</code></td>
                <td>@lang('Transaction reference already exists')</td>
                <td>@lang('Use a unique transaction reference')</td>
            </tr>
            <tr>
                <td><code>EXPIRED_SESSION</code></td>
                <td>@lang('Payment session has expired')</td>
                <td>@lang('Create a new payment request')</td>
            </tr>
            <tr>
                <td><code>MERCHANT_SUSPENDED</code></td>
                <td>@lang('Merchant account is suspended')</td>
                <td>@lang('Contact REEXPAY support')</td>
            </tr>
        </tbody>
    </table>

    <!-- Error Response Format -->
    <h3>{{ __('Error Response Format') }}</h3>
    <div class="code-block">
        <pre><code class="json">{
  "success": false,
  "message": "Validation failed",
  "error_code": "INVALID_AMOUNT",
  "errors": {
    "payment_amount": [
      "The payment amount must be at least 1.00"
    ]
  },
  "timestamp": "2024-01-20T10:30:00Z"
}</code></pre>
    </div>

    <div class="api-alert api-alert-warning">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>{{ __('Error Handling') }}</strong>
        {{ __('Always check the success field in API responses and handle errors appropriately in your application.') }}
    </div>
</section>

<!-- Support Section -->
<section id="support" class="content-section">
    <h2 class="mb-4">@lang('Support')</h2>
    <p>@lang('Get help with your REEXPAY integration from our support team and developer resources.')</p>
    
    <article class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-headset me-2"></i>
                @lang('Technical Support')
            </h5>
        </div>
        
        <div class="card-body">
            <p class="fw-semibold">@lang('For API integration help:')</p>
            <ul class="list-unstyled mb-4">
                <li>
                    <span class="fw-semibold">@lang('Email:')</span>
                    <a href="mailto:api-support@digikash.com" class="link-primary">contact.coevs@gmail.com</a>
                </li>
                <li>
                    <span class="fw-semibold">@lang('Response Time:')</span>
                    <span>@lang('24–48 hours')</span>
                </li>
                <li>
                    <span class="fw-semibold">@lang('Hours:')</span>
                    <span>@lang('Monday–Friday, 9 AM–6 PM UTC')</span>
                </li>
            </ul>
            
            <p class="text-muted small">
                @lang('Include your Merchant ID and detailed error descriptions for faster resolution.')
            </p>
            
            <a href="mailto:contact.coevs@gmail.com" class="btn btn-primary">
                @lang('Contact Support')
            </a>
        </div>
    </article>
</section>
