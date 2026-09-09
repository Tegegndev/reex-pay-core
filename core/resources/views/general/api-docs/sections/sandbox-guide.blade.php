<section id="sandbox-guide" class="content-section">
    <h2>Sandbox Testing Guide</h2>
    <p>Complete guide to testing REEXPAY API integration in sandbox environment. Test all features safely without processing real money.</p>

    <!-- Environment Overview -->
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-flask me-3 fs-4"></i>
            <div>
                <h6 class="mb-1">{{ __('Sandbox Environment') }}</h6>
                <p class="mb-0">{{ __('Sandbox mode provides a complete testing environment that mirrors production functionality. All transactions are simulated with no real money processing.') }}</p>
            </div>
        </div>
    </div>

    <!-- Getting Started -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-rocket me-2"></i>Getting Started with Sandbox</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <h6 class="text-primary"><i class="fas fa-key me-2"></i>1. Get Sandbox Credentials</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>Login to your REEXPAY merchant dashboard</li>
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>Navigate to <strong>API Configuration</strong></li>
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>Switch to <span class="badge bg-warning">Sandbox Mode</span></li>
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>Copy your <code>test_</code> prefixed credentials</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-success"><i class="fas fa-code me-2"></i>2. Configure Your Integration</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>Set <code>X-Environment: sandbox</code> header</li>
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>Use <code>test_</code> prefixed API credentials</li>
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>Configure webhook endpoints for testing</li>
                        <li><i class="fas fa-arrow-right text-muted me-2"></i>Enable sandbox mode in your merchant settings</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Credential Configuration -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Sandbox Credentials</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Credential Type</th>
                            <th>Sandbox Format</th>
                            <th>Production Format</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>API Key</strong></td>
                            <td><code>test_api_xxxxxxxxxx</code></td>
                            <td><code>api_xxxxxxxxxx</code></td>
                            <td>Authentication for API requests</td>
                        </tr>
                        <tr>
                            <td><strong>Merchant Key</strong></td>
                            <td><code>test_merchant_xxxxx</code></td>
                            <td><code>merchant_xxxxx</code></td>
                            <td>Merchant identification</td>
                        </tr>
                        <tr>
                            <td><strong>Webhook Secret</strong></td>
                            <td><code>test_webhook_secret</code></td>
                            <td><code>webhook_secret</code></td>
                            <td>IPN signature verification</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Testing Scenarios -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Comprehensive Testing Scenarios</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success"><i class="fas fa-credit-card me-2"></i>Payment Testing</h6>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <div>
                                <strong>Successful Payments</strong>
                                <small class="d-block text-muted">Test successful transaction flow</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-times-circle text-danger me-3"></i>
                            <div>
                                <strong>Failed Payments</strong>
                                <small class="d-block text-muted">Test insufficient balance scenarios</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-clock text-warning me-3"></i>
                            <div>
                                <strong>Pending Payments</strong>
                                <small class="d-block text-muted">Test pending transaction handling</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-ban text-secondary me-3"></i>
                            <div>
                                <strong>Cancelled Payments</strong>
                                <small class="d-block text-muted">Test user cancellation flow</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-info"><i class="fas fa-wallet me-2"></i>Wallet & Features Testing</h6>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-coins text-primary me-3"></i>
                            <div>
                                <strong>Multi-Currency</strong>
                                <small class="d-block text-muted">Test USD, EUR, BDT currencies</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-credit-card text-info me-3"></i>
                            <div>
                                <strong>Virtual Cards</strong>
                                <small class="d-block text-muted">Test card issuance and management</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-exchange-alt text-success me-3"></i>
                            <div>
                                <strong>Deposits & Withdrawals</strong>
                                <small class="d-block text-muted">Test wallet funding methods</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-id-card text-warning me-3"></i>
                            <div>
                                <strong>KYC Verification</strong>
                                <small class="d-block text-muted">Test document verification flow</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sandbox Features -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Sandbox-Specific Features</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="text-center p-3">
                        <i class="fas fa-tag fa-2x text-warning mb-3"></i>
                        <h6>Transaction Marking</h6>
                        <p class="text-muted small">All sandbox transactions are marked with <code>SANDBOX_TRANSACTION</code> in remarks field</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="text-center p-3">
                        <i class="fas fa-infinity fa-2x text-success mb-3"></i>
                        <h6>Unlimited Testing</h6>
                        <p class="text-muted small">No limits on number of test transactions or API calls</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="text-center p-3">
                        <i class="fas fa-sync-alt fa-2x text-info mb-3"></i>
                        <h6>Real-time Webhooks</h6>
                        <p class="text-muted small">Test webhook notifications with sandbox transaction data</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sample Integration Code -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-code me-2"></i>Sample Sandbox Integration</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="sandboxCodeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="php-sandbox-tab" data-bs-toggle="tab" data-bs-target="#php-sandbox" type="button">
                        <i class="fab fa-php text-primary me-1"></i>PHP
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="js-sandbox-tab" data-bs-toggle="tab" data-bs-target="#js-sandbox" type="button">
                        <i class="fab fa-js-square text-warning me-1"></i>JavaScript
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="curl-sandbox-tab" data-bs-toggle="tab" data-bs-target="#curl-sandbox" type="button">
                        <i class="fas fa-terminal text-success me-1"></i>cURL
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="sandboxCodeTabsContent">
                <!-- PHP Example -->
                <div class="tab-pane fade show active" id="php-sandbox" role="tabpanel">
                    <div class="code-block">
                        <pre><code class="php">&lt;?php
// REEXPAY Sandbox Integration Example
class REEXPAYSandboxTester
{
    private $apiKey = 'test_your_api_key_here';
    private $merchantKey = 'test_merchant_your_key_here';
    private $environment = 'sandbox';
    private $baseUrl = 'https://reexpaylimited.com/api/v1';

    public function testPaymentFlow()
    {
        // 1. Initiate Payment
        $paymentData = [
            'payment_amount' => 100.00,
            'currency_code' => 'USD',
            'ref_trx' => 'SANDBOX_TEST_' . uniqid(),
            'description' => 'Sandbox Payment Test',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'success_redirect' => 'https://yoursite.com/success',
            'failure_url' => 'https://yoursite.com/failure',
            'cancel_redirect' => 'https://yoursite.com/cancel',
            'ipn_url' => 'https://yoursite.com/webhook/digikash'
        ];

        $response = $this->makeApiRequest('POST', '/initiate-payment', $paymentData);
        
        if ($response['success']) {
            echo "Payment URL: " . $response['data']['payment_url'] . "\n";
            
            // 2. Simulate payment completion and verify
            $trxId = $response['data']['info']['transaction_id'] ?? 'TXN123456789';
            $this->verifyPayment($trxId);
        }
    }

    public function verifyPayment($trxId)
    {
        $response = $this->makeApiRequest('GET', "/verify-payment/{$trxId}");
        echo "Payment Status: " . $response['data']['status'] . "\n";
    }

    private function makeApiRequest($method, $endpoint, $data = null)
    {
        $headers = [
            'X-Environment: ' . $this->environment,
            'X-API-Key: ' . $this->apiKey,
            'X-Merchant-Key: ' . $this->merchantKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}

// Run sandbox tests
$tester = new REEXPAYSandboxTester();
$tester->testPaymentFlow();</code></pre>
                    </div>
                </div>

                <!-- JavaScript Example -->
                <div class="tab-pane fade" id="js-sandbox" role="tabpanel">
                    <div class="code-block">
                        <pre><code class="javascript">// REEXPAY Sandbox Testing Suite
class REEXPAYSandboxTester {
    constructor() {
        this.apiKey = 'test_your_api_key_here';
        this.merchantKey = 'test_merchant_your_key_here';
        this.environment = 'sandbox';
        this.baseUrl = 'https://reexpaylimited.com/api/v1';
    }

    async testPaymentFlow() {
        try {
            // 1. Initiate Payment
            const paymentData = {
                payment_amount: 100.00,
                currency_code: 'USD',
                ref_trx: 'SANDBOX_TEST_' + Date.now(),
                description: 'Sandbox Payment Test',
                customer_name: 'John Doe',
                customer_email: 'john@example.com',
                success_redirect: 'https://yoursite.com/success',
                failure_url: 'https://yoursite.com/failure',
                cancel_redirect: 'https://yoursite.com/cancel',
                ipn_url: 'https://yoursite.com/webhook/digikash'
            };

            const response = await this.makeApiRequest('POST', '/initiate-payment', paymentData);
            
            if (response.payment_url) {
                console.log('Payment URL:', response.payment_url);
                
                // 2. Simulate verification
                const trxId = response.info.transaction_id || 'TXN123456789';
                await this.verifyPayment(trxId);
            }
        } catch (error) {
            console.error('Sandbox test failed:', error);
        }
    }

    async verifyPayment(trxId) {
        const response = await this.makeApiRequest('GET', `/verify-payment/${trxId}`);
        console.log('Payment Status:', response.status);
    }

    async makeApiRequest(method, endpoint, data = null) {
        const headers = {
            'X-Environment': this.environment,
            'X-API-Key': this.apiKey,
            'X-Merchant-Key': this.merchantKey,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        const config = {
            method,
            headers
        };

        if (data) {
            config.body = JSON.stringify(data);
        }

        const response = await fetch(this.baseUrl + endpoint, config);
        return await response.json();
    }
}

// Run sandbox tests
const tester = new REEXPAYSandboxTester();
tester.testPaymentFlow();</code></pre>
                    </div>
                </div>

                <!-- cURL Example -->
                <div class="tab-pane fade" id="curl-sandbox" role="tabpanel">
                    <div class="code-block">
                        <pre><code class="bash">#!/bin/bash
# REEXPAY Sandbox Testing Script

# Configuration
API_KEY="test_your_api_key_here"
MERCHANT_KEY="test_merchant_your_key_here"
ENVIRONMENT="sandbox"
BASE_URL="https://reexpaylimited.com/api/v1"

# Test Payment Initiation
echo "Testing Payment Initiation..."
curl -X POST "${BASE_URL}/initiate-payment" \
  -H "X-Environment: ${ENVIRONMENT}" \
  -H "X-API-Key: ${API_KEY}" \
  -H "X-Merchant-Key: ${MERCHANT_KEY}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "payment_amount": 100.00,
    "currency_code": "USD",
    "ref_trx": "SANDBOX_TEST_'$(date +%s)'",
    "description": "Sandbox Payment Test",
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "success_redirect": "https://yoursite.com/success",
    "failure_url": "https://yoursite.com/failure",
    "cancel_redirect": "https://yoursite.com/cancel",
    "ipn_url": "https://yoursite.com/webhook/digikash"
  }' | jq .

# Test Payment Verification
echo -e "\nTesting Payment Verification..."
TRX_ID="TXN123456789"  # Replace with actual transaction ID
curl -X GET "${BASE_URL}/verify-payment/${TRX_ID}" \
  -H "X-Environment: ${ENVIRONMENT}" \
  -H "X-API-Key: ${API_KEY}" \
  -H "X-Merchant-Key: ${MERCHANT_KEY}" \
  -H "Accept: application/json" | jq .</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Practices -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Sandbox Best Practices</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Do's</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Always test in sandbox before production</li>
                        <li><i class="fas fa-check text-success me-2"></i>Test all payment scenarios (success, failure, pending)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Verify webhook/IPN handling thoroughly</li>
                        <li><i class="fas fa-check text-success me-2"></i>Test with different currencies and amounts</li>
                        <li><i class="fas fa-check text-success me-2"></i>Store credentials in environment variables</li>
                        <li><i class="fas fa-check text-success me-2"></i>Log all API interactions for debugging</li>
                        <li><i class="fas fa-check text-success me-2"></i>Test error handling and edge cases</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-danger"><i class="fas fa-times-circle me-2"></i>Don'ts</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-times text-danger me-2"></i>Don't use production credentials in sandbox</li>
                        <li><i class="fas fa-times text-danger me-2"></i>Don't skip webhook signature verification</li>
                        <li><i class="fas fa-times text-danger me-2"></i>Don't hardcode credentials in your code</li>
                        <li><i class="fas fa-times text-danger me-2"></i>Don't ignore error responses</li>
                        <li><i class="fas fa-times text-danger me-2"></i>Don't test with real customer data</li>
                        <li><i class="fas fa-times text-danger me-2"></i>Don't deploy without thorough testing</li>
                        <li><i class="fas fa-times text-danger me-2"></i>Don't mix sandbox and production data</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Troubleshooting -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-bug me-2"></i>Common Issues & Solutions</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="troubleshootingAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue1">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Authentication Failed (401 Unauthorized)
                        </button>
                    </h2>
                    <div id="issue1" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                        <div class="accordion-body">
                            <strong>Common Causes:</strong>
                            <ul>
                                <li>Using production credentials with sandbox environment</li>
                                <li>Missing or incorrect <code>X-Environment</code> header</li>
                                <li>Invalid API key or merchant key format</li>
                                <li>Sandbox mode disabled in merchant settings</li>
                            </ul>
                            <strong>Solutions:</strong>
                            <ul>
                                <li>Verify you're using <code>test_</code> prefixed credentials</li>
                                <li>Ensure <code>X-Environment: sandbox</code> header is set</li>
                                <li>Check credentials in merchant dashboard</li>
                                <li>Enable sandbox mode in merchant settings</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue2">
                            <i class="fas fa-ban text-danger me-2"></i>
                            Sandbox Disabled Error (403 Forbidden)
                        </button>
                    </h2>
                    <div id="issue2" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                        <div class="accordion-body">
                            <strong>Cause:</strong> Sandbox mode is not enabled for your merchant account.
                            <br><strong>Solution:</strong>
                            <ul>
                                <li>Login to merchant dashboard</li>
                                <li>Navigate to API Configuration</li>
                                <li>Enable "Sandbox Mode"</li>
                                <li>Generate test credentials if not already available</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#issue3">
                            <i class="fas fa-clock text-info me-2"></i>
                            Webhook Not Received
                        </button>
                    </h2>
                    <div id="issue3" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                        <div class="accordion-body">
                            <strong>Troubleshooting Steps:</strong>
                            <ul>
                                <li>Verify webhook URL is publicly accessible</li>
                                <li>Check if your server accepts POST requests</li>
                                <li>Ensure webhook endpoint returns 200 OK status</li>
                                <li>Check server logs for incoming requests</li>
                                <li>Use tools like ngrok for local testing</li>
                                <li>Verify webhook signature verification code</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ready for Production -->
    <div class="alert alert-success">
        <div class="d-flex align-items-center">
            <i class="fas fa-rocket me-3 fs-4"></i>
            <div>
                <h6 class="mb-1">{{ __('Ready for Production?') }}</h6>
                <p class="mb-0">{{ __('Once you have thoroughly tested all scenarios in sandbox, switch to production mode, update your credentials, and set X-Environment header to "production".') }}</p>
            </div>
        </div>
    </div>
</section>
