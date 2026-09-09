<!-- Initiate Payment Section -->
<section id="initiate-payment" class="content-section">
    <h2>{{ __('Initiate Payment') }}</h2>
    <p>{{ __('Create a new payment request and get a secure checkout URL for your customer. This endpoint works in both sandbox and production environments based on your X-Environment header.') }}</p>

    <!-- Endpoint Information -->
    <div class="response-example">
        <div class="response-header">
            <span class="method-badge method-post">POST</span>
            <code>/api/v1/initiate-payment</code>
        </div>
    </div>

    <!-- Headers -->
    <h3>{{ __('Request Headers') }}</h3>
    <table class="api-table">
        <thead>
            <tr>
                <th>{{ __('Header') }}</th>
                <th>{{ __('Value') }}</th>
                <th>{{ __('Required') }}</th>
                <th>{{ __('Description') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>Content-Type</code></td>
                <td><code>application/json</code></td>
                <td>✅</td>
                <td>{{ __('Request content type') }}</td>
            </tr>
            <tr>
                <td><code>X-Environment</code></td>
                <td><code>sandbox</code> | <code>production</code></td>
                <td>✅</td>
                <td>{{ __('API environment mode') }}</td>
            </tr>
            <tr>
                <td><code>X-Merchant-Key</code></td>
                <td><code>{merchant_key}</code></td>
                <td>✅</td>
                <td>{{ __('Your Merchant ID') }} <small class="text-muted">({{ __('sandbox: test_ prefix, production: no prefix') }})</small></td>
            </tr>
            <tr>
                <td><code>X-API-Key</code></td>
                <td><code>{api_key}</code></td>
                <td>✅</td>
                <td>{{ __('Your API Key') }} <small class="text-muted">({{ __('sandbox: test_ prefix, production: no prefix') }})</small></td>
            </tr>
        </tbody>
    </table>

    <!-- Request Parameters -->
    <h3>{{ __('Request Parameters') }}</h3>
    <table class="api-table">
        <thead>
            <tr>
                <th>{{ __('Parameter') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Required') }}</th>
                <th>{{ __('Description') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>payment_amount</code></td>
                <td>number</td>
                <td>✅</td>
                <td>{{ __('Payment amount (minimum 1.00)') }}</td>
            </tr>
            <tr>
                <td><code>currency_code</code></td>
                <td>string</td>
                <td>✅</td>
                <td>{{ __('3-letter currency code (USD, EUR, etc.)') }}</td>
            </tr>
            <tr>
                <td><code>ref_trx</code></td>
                <td>string</td>
                <td>✅</td>
                <td>{{ __('Your unique transaction reference') }}</td>
            </tr>
            <tr>
                <td><code>description</code></td>
                <td>string</td>
                <td>❌</td>
                <td>{{ __('Payment description') }}</td>
            </tr>
            <tr>
                <td><code>success_redirect</code></td>
                <td>string</td>
                <td>✅</td>
                <td>{{ __('Success redirect URL') }}</td>
            </tr>
            <tr>
                <td><code>failure_url</code></td>
                <td>string</td>
                <td>✅</td>
                <td>{{ __('Failure redirect URL') }}</td>
            </tr>
            <tr>
                <td><code>cancel_redirect</code></td>
                <td>string</td>
                <td>✅</td>
                <td>{{ __('Cancel redirect URL') }}</td>
            </tr>
            <tr>
                <td><code>ipn_url</code></td>
                <td>string</td>
                <td>✅</td>
                <td>{{ __('Webhook notification URL (same URL for both environments)') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Code Examples -->
    <h3>{{ __('Code Examples') }}</h3>
    
    <!-- Environment Note -->
    <div class="alert alert-warning mb-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>{{ __('Environment Configuration') }}:</strong> {{ __('Replace {environment} with sandbox or production, and use corresponding credentials - test_ prefix for sandbox, no prefix for production.') }}
    </div>
    
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="initiatePaymentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="curl-initiate-tab" data-bs-toggle="tab" data-bs-target="#curl-initiate" type="button" role="tab">
                <i class="fa-solid fa-terminal text-dark me-1" aria-hidden="true"></i>{{ __('cURL') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="php-initiate-tab" data-bs-toggle="tab" data-bs-target="#php-initiate" type="button" role="tab">
                <i class="fa-brands fa-php text-primary me-1" aria-hidden="true"></i>{{ __('PHP/Laravel') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="nodejs-initiate-tab" data-bs-toggle="tab" data-bs-target="#nodejs-initiate" type="button" role="tab">
                <i class="fa-brands fa-node-js text-success me-1" aria-hidden="true"></i>{{ __('Node.js') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="python-initiate-tab" data-bs-toggle="tab" data-bs-target="#python-initiate" type="button" role="tab">
                <i class="fa-brands fa-python text-primary me-1" aria-hidden="true"></i>{{ __('Python') }}
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="initiatePaymentTabContent">
        <!-- cURL Example -->
        <div class="tab-pane fade show active" id="curl-initiate" role="tabpanel">
            <div class="code-block">
                <pre><code class="bash">curl -X POST "https://reexpaylimited.com/api/v1/initiate-payment" \
  -H "Content-Type: application/json" \
  -H "X-Environment: {environment}" \
  -H "X-Merchant-Key: {merchant_key}" \
  -H "X-API-Key: {api_key}" \
  -d '{"payment_amount": 250.00, "currency_code": "USD", "ref_trx": "ORDER_12345", "description": "Premium Subscription", "success_redirect": "https://yoursite.com/payment/success", "failure_url": "https://yoursite.com/payment/failed", "cancel_redirect": "https://yoursite.com/payment/cancelled", "ipn_url": "https://yoursite.com/api/ipn/marzpay"}'</code></pre>
            </div>
        </div>

        <!-- PHP/Laravel Example -->
        <div class="tab-pane fade" id="php-initiate" role="tabpanel">
            <div class="code-block">
                <pre><code class="php">&lt;?php

use App\Enums\EnvironmentMode;
use Illuminate\Support\Facades\Http;

class REEXPAYPaymentInitiator
{
    private $environment;
    private $merchantKey;
    private $apiKey;
    private $baseUrl = 'https://reexpaylimited.com/api/v1';

    public function __construct(EnvironmentMode $environment, $merchantKey, $apiKey)
    {
        $this->environment = $environment;
        $this->merchantKey = $merchantKey;
        $this->apiKey = $apiKey;
    }

    // Factory methods for easy configuration
    public static function sandbox($testMerchantKey, $testApiKey): self
    {
        return new self(EnvironmentMode::SANDBOX, $testMerchantKey, $testApiKey);
    }

    public static function production($merchantKey, $apiKey): self
    {
        return new self(EnvironmentMode::PRODUCTION, $merchantKey, $apiKey);
    }

    public function initiatePayment($paymentData)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Environment' => $this->environment->value,
                'X-Merchant-Key' => $this->merchantKey,
                'X-API-Key' => $this->apiKey,
            ])->post("{$this->baseUrl}/initiate-payment", $paymentData);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success']) {
                    return ['success' => true, 'data' => $data];
                }
                
                return ['success' => false, 'status' => $data['status'], 'message' => $data['message'] ?? 'Payment initiation failed'];
            }

            return ['success' => false, 'error' => 'API request failed'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

// Usage: Choose appropriate factory method based on your environment
$initiator = REEXPAYPaymentInitiator::sandbox('test_merchant_key', 'test_api_key'); // For testing
// $initiator = REEXPAYPaymentInitiator::production('merchant_key', 'api_key'); // For production

$paymentData = [
    'payment_amount' => 250.00,
    'currency_code' => 'USD',
    'ref_trx' => 'ORDER_12345',
    'description' => 'Premium Subscription',
    'success_redirect' => 'https://yoursite.com/payment/success',
    'failure_url' => 'https://yoursite.com/payment/failed',
    'cancel_redirect' => 'https://yoursite.com/payment/cancelled',
    'ipn_url' => 'https://yoursite.com/api/ipn/marzpay',
];

$result = $initiator->initiatePayment($paymentData);</code></pre>
            </div>
        </div>

        <!-- Node.js Example -->
        <div class="tab-pane fade" id="nodejs-initiate" role="tabpanel">
            <div class="code-block">
                <pre><code class="javascript">const axios = require('axios');

const EnvironmentMode = {
    SANDBOX: 'sandbox',
    PRODUCTION: 'production'
};

class REEXPAYPaymentInitiator {
    constructor(environment, merchantKey, apiKey) {
        this.environment = environment;
        this.merchantKey = merchantKey;
        this.apiKey = apiKey;
        this.baseUrl = 'https://reexpaylimited.com/api/v1';
    }

    // Factory methods
    static sandbox(testMerchantKey, testApiKey) {
        return new REEXPAYPaymentInitiator(EnvironmentMode.SANDBOX, testMerchantKey, testApiKey);
    }

    static production(merchantKey, apiKey) {
        return new REEXPAYPaymentInitiator(EnvironmentMode.PRODUCTION, merchantKey, apiKey);
    }

    async initiatePayment(paymentData) {
        try {
            const response = await axios.post(`${this.baseUrl}/initiate-payment`, paymentData, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Environment': this.environment,
                    'X-Merchant-Key': this.merchantKey,
                    'X-API-Key': this.apiKey
                }
            });

            const data = response.data;

            if (data.success) {
                return { success: true, data };
            }

            return { 
                success: false, 
                status: data.status, 
                message: data.message || 'Payment initiation failed' 
            };

        } catch (error) {
            if (error.response) {
                return { 
                    success: false, 
                    error: error.response.data.error || 'API request failed' 
                };
            }
            return { success: false, error: error.message };
        }
    }
}

// Usage: Choose appropriate factory method based on your environment
const initiator = REEXPAYPaymentInitiator.sandbox('test_merchant_key', 'test_api_key'); // For testing
// const initiator = REEXPAYPaymentInitiator.production('merchant_key', 'api_key'); // For production

const paymentData = {
    payment_amount: 250.00,
    currency_code: 'USD',
    ref_trx: 'ORDER_12345',
    description: 'Premium Subscription',
    success_redirect: 'https://yoursite.com/payment/success',
    failure_url: 'https://yoursite.com/payment/failed',
    cancel_redirect: 'https://yoursite.com/payment/cancelled',
    ipn_url: 'https://yoursite.com/api/ipn/marzpay',
};

initiator.initiatePayment(paymentData)
    .then(result => console.log(result))
    .catch(error => console.error(error));</code></pre>
            </div>
        </div>

        <!-- Python Example -->
        <div class="tab-pane fade" id="python-initiate" role="tabpanel">
            <div class="code-block">
                <pre><code class="python">import requests
import logging
from enum import Enum

class EnvironmentMode(Enum):
    SANDBOX = 'sandbox'
    PRODUCTION = 'production'

class REEXPAYPaymentInitiator:
    def __init__(self, environment, merchant_key, api_key):
        self.environment = environment
        self.merchant_key = merchant_key
        self.api_key = api_key
        self.base_url = 'https://reexpaylimited.com/api/v1'

    @classmethod
    def sandbox(cls, test_merchant_key, test_api_key):
        return cls(EnvironmentMode.SANDBOX, test_merchant_key, test_api_key)

    @classmethod
    def production(cls, merchant_key, api_key):
        return cls(EnvironmentMode.PRODUCTION, merchant_key, api_key)

    def initiate_payment(self, payment_data):
        try:
            headers = {
                'Content-Type': 'application/json',
                'X-Environment': self.environment.value,
                'X-Merchant-Key': self.merchant_key,
                'X-API-Key': self.api_key
            }

            response = requests.post(
                f"{self.base_url}/initiate-payment",
                headers=headers,
                json=payment_data,
                timeout=30
            )

            if response.status_code == 200:
                data = response.json()
                
                if data['success']:
                    return {'success': True, 'data': data}
                
                return {
                    'success': False, 
                    'status': data['status'], 
                    'message': data.get('message', 'Payment initiation failed')
                }

            return {'success': False, 'error': f'HTTP {response.status_code}'}

        except requests.RequestException as e:
            return {'success': False, 'error': str(e)}

# Usage: Choose appropriate factory method based on your environment
initiator = REEXPAYPaymentInitiator.sandbox('test_merchant_key', 'test_api_key')  # For testing
# initiator = REEXPAYPaymentInitiator.production('merchant_key', 'api_key')  # For production

payment_data = {
    'payment_amount': 250.00,
    'currency_code': 'USD',
    'ref_trx': 'ORDER_12345',
    'description': 'Premium Subscription',
    'success_redirect': 'https://yoursite.com/payment/success',
    'failure_url': 'https://yoursite.com/payment/failed',
    'cancel_redirect': 'https://yoursite.com/payment/cancelled',
    'ipn_url': 'https://yoursite.com/api/ipn/marzpay',
}

result = initiator.initiate_payment(payment_data)
print(result)</code></pre>
            </div>
        </div>
    </div>

    <!-- Success Response -->
    <h3>{{ __('Success Response') }}</h3>
    <div class="response-example">
        <div class="response-header">
            <span class="status-code status-200">200 OK</span>
            {{ __('Success') }}
        </div>
        <div class="response-body">
            <div class="code-block">
                <pre><code class="json">{
    "payment_url": "https://reexpaylimited.com/payment/checkout?expires=1753724376&token=AmQvJdGIdGUVJUUMayJZZreBv2UcTyIHclk9Ps1s1pZhLpVlIqIBVPqGTRKQ3NUSehyM3qRUIf69IhLbNfJ1JqiMxlxNrnn22lNz1N01hZQn65r5VZnvhWmQPxQO8UX6rE4yfRUvT6bHdqLj7UDJhRPYRFSgCsG1b86sxSdKTZNOVJdWV5z8L6a5pNMZ2KlpG5e7bYa&signature=e9q7ea91456dcc167e7d498ea486f923570821957be8881566186655950f364",
    "info": {
        "ref_trx": "TXNT4AQFESTAG4F",
        "description": "Order #1234",
        "ipn_url": "https://webhook.site/5711b7d5-917a-4d94-bbb3-c28f4a37bea5",
        "cancel_redirect": "https://merchant.com/cancel",
        "success_redirect": "https://merchant.com/success",
        "merchant_id": 1,
        "merchant_name": "Xanthus Wiggins", 
        "amount": 200,
        "currency_code": "USD",
        "environment": "production",
        "is_sandbox": false
    }
}</code></pre>
            </div>
        </div>
    </div>

    <!-- Error Response -->
    <h3>{{ __('Error Response') }}</h3>
    <div class="response-example">
        <div class="response-header">
            <span class="status-code status-400">400 Bad Request</span>
            {{ __('Error') }}
        </div>
        <div class="response-body">
            <div class="code-block">
                <pre><code class="json">{
  "success": false,
  "message": "{{ __('Validation failed') }}",
  "errors": {
    "payment_amount": ["{{ __('The payment amount field is required.') }}"],
    "currency_code": ["{{ __('The currency code field is required.') }}"]
  }
}</code></pre>
            </div>
        </div>
    </div>

    <div class="api-alert api-alert-info">
        <strong><i class="fas fa-info-circle me-2"></i>{{ __('Next Step') }}</strong>
        {{ __('After successful payment initiation, redirect your customer to the payment_url to complete the payment.') }}
    </div>
</section>

<!-- Verify Payment Section -->
<section id="verify-payment" class="content-section">
    <h2>{{ __('Verify Payment') }}</h2>
    <p>{{ __('Verify the status of a payment using the REEXPAY transaction ID returned from the payment initiation.') }}</p>

    <div class="response-example">
        <div class="response-header">
            <span class="method-badge method-get">GET</span>
            <code>/api/v1/verify-payment/{trxId}</code>
        </div>
    </div>

    <!-- Request Headers -->
    <h3>{{ __('Request Headers') }}</h3>
    <table class="api-table">
        <thead>
            <tr>
                <th>{{ __('Header') }}</th>
                <th>{{ __('Value') }}</th>
                <th>{{ __('Required') }}</th>
                <th>{{ __('Description') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>Accept</code></td>
                <td><code>application/json</code></td>
                <td>✅</td>
                <td>{{ __('Request content type') }}</td>
            </tr>
            <tr>
                <td><code>X-Environment</code></td>
                <td><code>sandbox</code> | <code>production</code></td>
                <td>✅</td>
                <td>{{ __('API environment mode') }}</td>
            </tr>
            <tr>
                <td><code>X-Merchant-Key</code></td>
                <td><code>{merchant_key}</code></td>
                <td>✅</td>
                <td>{{ __('Your Merchant ID') }} <small class="text-muted">({{ __('sandbox: test_ prefix, production: no prefix') }})</small></td>
            </tr>
            <tr>
                <td><code>X-API-Key</code></td>
                <td><code>{api_key}</code></td>
                <td>✅</td>
                <td>{{ __('Your API Key') }} <small class="text-muted">({{ __('sandbox: test_ prefix, production: no prefix') }})</small></td>
            </tr>
        </tbody>
    </table>

    <!-- Path Parameters -->
    <h3>{{ __('Path Parameters') }}</h3>
    <table class="api-table">
        <thead>
            <tr>
                <th>{{ __('Parameter') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Required') }}</th>
                <th>{{ __('Description') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>trxId</code></td>
                <td>string</td>
                <td>✅</td>
                <td>{{ __('REEXPAY transaction ID (e.g., TXNQ5V8K2L9N3XM1)') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Code Examples -->
    <h3>{{ __('Code Examples') }}</h3>
    
    <!-- Environment Note -->
    <div class="alert alert-warning mb-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>{{ __('Environment Configuration') }}:</strong> {{ __('Replace {environment} with sandbox or production, and use corresponding credentials - test_ prefix for sandbox, no prefix for production.') }}
    </div>
    
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="verifyPaymentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="curl-verify-tab" data-bs-toggle="tab" data-bs-target="#curl-verify" type="button" role="tab">
                <i class="fa-solid fa-terminal text-dark me-1" aria-hidden="true"></i>{{ __('cURL') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="php-verify-tab" data-bs-toggle="tab" data-bs-target="#php-verify" type="button" role="tab">
                <i class="fa-brands fa-php text-primary me-1" aria-hidden="true"></i>{{ __('PHP/Laravel') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="nodejs-verify-tab" data-bs-toggle="tab" data-bs-target="#nodejs-verify" type="button" role="tab">
                <i class="fa-brands fa-node-js text-success me-1" aria-hidden="true"></i>{{ __('Node.js') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="python-verify-tab" data-bs-toggle="tab" data-bs-target="#python-verify" type="button" role="tab">
                <i class="fa-brands fa-python text-primary me-1" aria-hidden="true"></i>{{ __('Python') }}
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="verifyPaymentTabContent">
        <!-- cURL Example -->
        <div class="tab-pane fade show active" id="curl-verify" role="tabpanel">
            <div class="code-block">
                <pre><code class="bash">curl -X GET "https://reexpaylimited.com/api/v1/verify-payment/TXNQ5V8K2L9N3XM1" \
  -H "Accept: application/json" \
  -H "X-Environment: {environment}" \
  -H "X-Merchant-Key: {merchant_key}" \
  -H "X-API-Key: {api_key}"</code></pre>
            </div>
        </div>

        <!-- PHP/Laravel Example -->
        <div class="tab-pane fade" id="php-verify" role="tabpanel">
            <div class="code-block">
                <pre><code class="php">&lt;?php

use App\Enums\EnvironmentMode;
use Illuminate\Support\Facades\Http;

class REEXPAYPaymentVerifier
{
    private $environment;
    private $merchantKey;
    private $apiKey;
    private $baseUrl = 'https://reexpaylimited.com/api/v1';

    public function __construct(EnvironmentMode $environment, $merchantKey, $apiKey)
    {
        $this->environment = $environment;
        $this->merchantKey = $merchantKey;
        $this->apiKey = $apiKey;
    }

    // Factory methods for easy configuration
    public static function sandbox($testMerchantKey, $testApiKey): self
    {
        return new self(EnvironmentMode::SANDBOX, $testMerchantKey, $testApiKey);
    }

    public static function production($merchantKey, $apiKey): self
    {
        return new self(EnvironmentMode::PRODUCTION, $merchantKey, $apiKey);
    }

    public function verifyPayment($trxId)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'X-Environment' => $this->environment->value,
                'X-Merchant-Key' => $this->merchantKey,
                'X-API-Key' => $this->apiKey,
            ])->get("{$this->baseUrl}/verify-payment/{$trxId}");

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    // Payment completed successfully
                    $this->fulfillOrder($data);
                    return ['success' => true, 'data' => $data];
                }
                
                return ['success' => false, 'status' => $data['status'], 'message' => $data['message'] ?? 'Payment not completed'];
            }

            return ['success' => false, 'error' => 'API request failed'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function fulfillOrder($paymentData)
    {
        // Your order fulfillment logic here
        logger('Payment verified successfully', $paymentData);
    }
}

// Usage: Choose appropriate factory method based on your environment
$verifier = REEXPAYPaymentVerifier::sandbox('test_merchant_key', 'test_api_key'); // For testing
// $verifier = REEXPAYPaymentVerifier::production('merchant_key', 'api_key'); // For production

$result = $verifier->verifyPayment('TXNQ5V8K2L9N3XM1');</code></pre>
            </div>
        </div>

        <!-- Node.js Example -->
        <div class="tab-pane fade" id="nodejs-verify" role="tabpanel">
            <div class="code-block">
                <pre><code class="javascript">const axios = require('axios');

const EnvironmentMode = {
    SANDBOX: 'sandbox',
    PRODUCTION: 'production'
};

class REEXPAYPaymentVerifier {
    constructor(environment, merchantKey, apiKey) {
        this.environment = environment;
        this.merchantKey = merchantKey;
        this.apiKey = apiKey;
        this.baseUrl = 'https://reexpaylimited.com/api/v1';
    }

    // Factory methods
    static sandbox(testMerchantKey, testApiKey) {
        return new REEXPAYPaymentVerifier(EnvironmentMode.SANDBOX, testMerchantKey, testApiKey);
    }

    static production(merchantKey, apiKey) {
        return new REEXPAYPaymentVerifier(EnvironmentMode.PRODUCTION, merchantKey, apiKey);
    }

    async verifyPayment(trxId) {
        try {
            const response = await axios.get(`${this.baseUrl}/verify-payment/${trxId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Environment': this.environment,
                    'X-Merchant-Key': this.merchantKey,
                    'X-API-Key': this.apiKey
                }
            });

            const data = response.data;

            if (data.status === 'success') {
                // Payment completed successfully
                await this.fulfillOrder(data);
                return { success: true, data };
            }

            return { 
                success: false, 
                status: data.status, 
                message: data.message || 'Payment not completed' 
            };

        } catch (error) {
            if (error.response) {
                return { 
                    success: false, 
                    error: error.response.data.error || 'API request failed' 
                };
            }
            return { success: false, error: error.message };
        }
    }

    async fulfillOrder(paymentData) {
        // Your order fulfillment logic here
        console.log('Payment verified successfully:', paymentData);
    }
}

// Usage: Choose appropriate factory method based on your environment
const verifier = REEXPAYPaymentVerifier.sandbox('test_merchant_key', 'test_api_key'); // For testing
// const verifier = REEXPAYPaymentVerifier.production('merchant_key', 'api_key'); // For production

verifier.verifyPayment('TXNQ5V8K2L9N3XM1')
    .then(result => console.log(result))
    .catch(error => console.error(error));</code></pre>
            </div>
        </div>

        <!-- Python Example -->
        <div class="tab-pane fade" id="python-verify" role="tabpanel">
            <div class="code-block">
                <pre><code class="python">import requests
import logging
from enum import Enum

class EnvironmentMode(Enum):
    SANDBOX = 'sandbox'
    PRODUCTION = 'production'

class REEXPAYPaymentVerifier:
    def __init__(self, environment, merchant_key, api_key):
        self.environment = environment
        self.merchant_key = merchant_key
        self.api_key = api_key
        self.base_url = 'https://reexpaylimited.com/api/v1'

    @classmethod
    def sandbox(cls, test_merchant_key, test_api_key):
        return cls(EnvironmentMode.SANDBOX, test_merchant_key, test_api_key)

    @classmethod
    def production(cls, merchant_key, api_key):
        return cls(EnvironmentMode.PRODUCTION, merchant_key, api_key)

    def verify_payment(self, trx_id):
        try:
            headers = {
                'Accept': 'application/json',
                'X-Environment': self.environment.value,
                'X-Merchant-Key': self.merchant_key,
                'X-API-Key': self.api_key
            }

            response = requests.get(
                f"{self.base_url}/verify-payment/{trx_id}",
                headers=headers,
                timeout=30
            )

            if response.status_code == 200:
                data = response.json()
                
                if data['status'] == 'success':
                    # Payment completed successfully
                    self.fulfill_order(data)
                    return {'success': True, 'data': data}
                
                return {
                    'success': False, 
                    'status': data['status'], 
                    'message': data.get('message', 'Payment not completed')
                }

            return {'success': False, 'error': f'HTTP {response.status_code}'}

        except requests.RequestException as e:
            return {'success': False, 'error': str(e)}

    def fulfill_order(self, payment_data):
        """Your order fulfillment logic here"""
        logging.info(f"Payment verified successfully: {payment_data}")

# Usage: Choose appropriate factory method based on your environment
verifier = REEXPAYPaymentVerifier.sandbox('test_merchant_key', 'test_api_key')  # For testing
# verifier = REEXPAYPaymentVerifier.production('merchant_key', 'api_key')  # For production

result = verifier.verify_payment('TXNQ5V8K2L9N3XM1')
print(result)</code></pre>
            </div>
        </div>
    </div>

    <!-- Success Response -->
    <h3>{{ __('Success Response') }}</h3>
    <div class="response-example">
        <div class="response-header">
            <span class="status-code status-200">200 OK</span>
            {{ __('Success') }}
        </div>
        <div class="response-body">
            <div class="code-block">
                <pre><code class="json">{
    "status": "success",
    "trx_id": "TXNQ5V8K2L9N3XM1",
    "amount": 237.5,
    "fee": 12.5,
    "currency": "USD",
    "net_amount": 237.5,
    "customer": {
        "name": "John Doe",
        "email": "john@example.com"
    },
    "description": "Premium Subscription Payment",
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:35:45.000000Z"
}</code></pre>
            </div>
        </div>
    </div>

    <h4>{{ __('Failed/Canceled Transaction Response') }}</h4>
    <div class="code-block">
        <pre><code class="json">{
    "status": "failed",
    "trx_id": "TXNQ5V8K2L9N3XM1",
    "message": "{{ __('Payment failed or canceled.') }}"
}</code></pre>
    </div>

    <h4>{{ __('Pending Transaction Response') }}</h4>
    <div class="code-block">
        <pre><code class="json">{
    "status": "pending",
    "trx_id": "TXNQ5V8K2L9N3XM1",
    "message": "{{ __('Payment is still pending.') }}"
}</code></pre>
    </div>

    <!-- Payment Status Values -->
    <h3>{{ __('Payment Status Values') }}</h3>
    <table class="api-table">
        <thead>
            <tr>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Action Required') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>pending</code></td>
                <td>{{ __('Payment is still processing') }}</td>
                <td>{{ __('Wait for webhook notification') }}</td>
            </tr>
            <tr>
                <td><code>completed</code></td>
                <td>{{ __('Payment was successful') }}</td>
                <td>{{ __('Fulfill order/service') }}</td>
            </tr>
            <tr>
                <td><code>failed</code></td>
                <td>{{ __('Payment failed') }}</td>
                <td>{{ __('Handle failed payment') }}</td>
            </tr>
            <tr>
                <td><code>cancelled</code></td>
                <td>{{ __('Payment was cancelled by user') }}</td>
                <td>{{ __('Handle cancellation') }}</td>
            </tr>
            <tr>
                <td><code>expired</code></td>
                <td>{{ __('Payment session expired') }}</td>
                <td>{{ __('Create new payment') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="api-alert api-alert-warning">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>{{ __('Rate Limiting') }}</strong>
        {{ __('This endpoint is rate-limited to 60 requests per minute per merchant.') }}
    </div>
</section>
