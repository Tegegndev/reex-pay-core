<!-- Testing Section -->
<section id="testing" class="content-section">
    <h2>{{ __('Interactive API Testing') }}</h2>
    <p>{{ __('Test REEXPAY API endpoints directly from this documentation. Use the demo credentials below for sandbox testing.') }}</p>

    {{-- Demo Payment Information Card --}}
    <div class="alert alert-info border-0 mb-4" style="background: linear-gradient(135deg, #e3f2fd, #f3e5f5);">
        <div class="row">
            <div class="col-12">
                <h5 class="text-primary mb-3">
                    <i class="fas fa-flask me-2"></i>
                    {{ __('Demo Payment Information') }}
                    <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">{{ __('SANDBOX MODE') }}</span>
                </h5>
                <p class="mb-3 text-muted">{{ __('Use these demo credentials to test all payment methods in sandbox environment') }}:</p>
            </div>
        </div>
        
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-wallet me-2"></i>{{ __('Demo Wallet') }}
                        </h6>
                        <div class="demo-credential">
                            <strong>{{ __('Wallet ID') }}:</strong>
                            <code class="d-block bg-light p-2 rounded mt-1 mb-2">123456789</code>
                            <strong>{{ __('Password') }}:</strong>
                            <code class="d-block bg-light p-2 rounded mt-1">demo123</code>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            {{ __('Auto-approved in sandbox') }}
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-ticket-alt me-2"></i>{{ __('Demo Voucher') }}
                        </h6>
                        <div class="demo-credential">
                            <strong>{{ __('Voucher Code') }}:</strong>
                            <code class="d-block bg-light p-2 rounded mt-1">TESTVOUCHER</code>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            {{ __('Instant redemption') }}
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-credit-card me-2"></i>{{ __('Gateway Payment') }}
                        </h6>
                        <div class="demo-credential">
                            <strong>{{ __('Behavior') }}:</strong>
                            <code class="d-block bg-light p-2 rounded mt-1">{{ __('Auto Success') }}</code>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            {{ __('No external redirection') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="bg-white rounded p-3 border">
                    <h6 class="text-dark mb-2">
                        <i class="fas fa-info-circle me-2"></i>{{ __('Testing Guidelines') }}
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li><strong>{{ __('Environment Header') }}:</strong> {{ __('Always include') }} <code>X-ENVIRONMENT: sandbox</code> {{ __('in your API requests') }}</li>
                        <li><strong>{{ __('Demo Credentials') }}:</strong> {{ __('Use the provided demo wallet/voucher codes for testing payment flows') }}</li>
                        <li><strong>{{ __('Sandbox Behavior') }}:</strong> {{ __('All payments auto-complete successfully without real money processing') }}</li>
                        <li><strong>{{ __('Transaction Status') }}:</strong> {{ __('Sandbox transactions are marked with "SANDBOX_TRANSACTION" in remarks') }}</li>
                        <li><strong>{{ __('IPN Notifications') }}:</strong> {{ __('Webhook notifications work normally in sandbox mode') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Environment Configuration -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>{{ __('Environment Setup') }}:</strong> {{ __('Use') }} <code>sandbox</code> {{ __('for testing and') }} <code>production</code> {{ __('for live transactions. Only sandbox credentials use') }} <code>test_</code> {{ __('prefix, production credentials have no prefix.') }}
    </div>

    <!-- API Testing Interface -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-play-circle me-2"></i>{{ __('API Testing Console') }}</h5>
        </div>
        <div class="card-body">
            <!-- Endpoint Selection -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="testEndpoint" class="form-label fw-semibold">{{ __('Select Endpoint') }}:</label>
                    <select class="form-select" id="testEndpoint">
                        <option value="initiate-payment">{{ __('POST /api/v1/initiate-payment') }}</option>
                        <option value="verify-payment">{{ __('GET /api/v1/verify-payment/{trx_id}') }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="testEnvironment" class="form-label fw-semibold">{{ __('Environment') }}:</label>
                    <select class="form-select" id="testEnvironment">
                        <option value="sandbox">{{ __('Sandbox (Recommended for testing)') }}</option>
                        <option value="production">{{ __('Production (Live transactions)') }}</option>
                    </select>
                </div>
            </div>

            <!-- Headers Configuration -->
            <h6 class="fw-semibold mb-3"><i class="fas fa-key me-2"></i>{{ __('Authentication Headers') }}</h6>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="testApiKey" class="form-label">{{ __('API Key') }}:</label>
                    <input type="text" class="form-control" id="testApiKey" placeholder="{{ __('Enter your API key') }}">
                    <small class="text-muted">{{ __('Sandbox: test_*, Production: no prefix') }}</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="testMerchantKey" class="form-label">{{ __('Merchant Key') }}:</label>
                    <input type="text" class="form-control" id="testMerchantKey" placeholder="{{ __('Enter your merchant key') }}">
                    <small class="text-muted">{{ __('Sandbox: test_*, Production: no prefix') }}</small>
                </div>
            </div>

            <!-- Request Body -->
            <div id="requestBodySection">
                <h6 class="fw-semibold mb-3"><i class="fas fa-code me-2"></i>{{ __('Request Parameters') }}</h6>
                
                <!-- Initiate Payment Parameters -->
                <div id="initiatePaymentParams">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="paymentAmount" class="form-label">{{ __('Payment Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="paymentAmount" placeholder="250.00" step="0.01" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="currencyCode" class="form-label">{{ __('Currency Code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" id="currencyCode" placeholder="USD" maxlength="6">
                            <small class="text-muted">{{ __('Currency code must be uppercase (e.g. USD, EUR, BDT). You must use the currency that matches your merchant shop setup.') }}</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="refTrx" class="form-label">{{ __('Reference Transaction') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="refTrx" placeholder="ORDER_12345">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <input type="text" class="form-control" id="description" placeholder="{{ __('Payment for order #12345') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="successRedirect" class="form-label">{{ __('Success Redirect URL') }} <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="successRedirect" placeholder="https://merchant.com/success">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="failureUrl" class="form-label">{{ __('Failure URL') }} <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="failureUrl" placeholder="https://merchant.com/failure">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cancelRedirect" class="form-label">{{ __('Cancel Redirect URL') }}</label>
                            <input type="url" class="form-control" id="cancelRedirect" placeholder="https://merchant.com/cancel">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ipnUrl" class="form-label">{{ __('IPN URL') }}</label>
                            <input type="url" class="form-control" id="ipnUrl" placeholder="https://merchant.com/webhook/digikash">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customerName" class="form-label">{{ __('Customer Name') }}</label>
                            <input type="text" class="form-control" id="customerName" placeholder="John Doe">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customerEmail" class="form-label">{{ __('Customer Email') }}</label>
                            <input type="email" class="form-control" id="customerEmail" placeholder="john@example.com">
                        </div>
                    </div>
                </div>

                <!-- Verify Payment Parameters -->
                <div id="verifyPaymentParams" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="transactionId" class="form-label">{{ __('Transaction ID') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="transactionId" placeholder="TXNQ5V8K2L9N3XM1">
                            <small class="text-muted">{{ __('Enter the transaction ID you want to verify') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Button -->
            <div class="d-grid gap-2 mb-4">
                <button class="btn btn-success btn-lg" id="testApiBtn">
                    <i class="fas fa-play me-2"></i>{{ __('Send Test Request') }}
                </button>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center mb-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ __('Loading...') }}</span>
                </div>
                <p class="mt-2 text-muted">{{ __('Sending request...') }}</p>
            </div>

            <!-- Response Section -->
            <div id="responseSection" style="display: none;">
                <h6 class="fw-semibold mb-3"><i class="fas fa-server me-2"></i>{{ __('Response') }}</h6>
                
                <!-- Status Code -->
                <div class="mb-3">
                    <span class="badge" id="statusBadge"></span>
                    <span class="ms-2 text-muted" id="responseTime"></span>
                </div>

                <!-- Response Headers -->
                <div class="mb-3">
                    <h6 class="fw-semibold">{{ __('Response Headers') }}:</h6>
                    <pre class="bg-light p-3 rounded" id="responseHeaders"></pre>
                </div>

                <!-- Response Body -->
                <div class="mb-3">
                    <h6 class="fw-semibold">{{ __('Response Body') }}:</h6>
                    <pre class="bg-dark text-light p-3 rounded" id="responseBody"></pre>
                </div>

                <!-- Copy Response Button -->
                <button class="btn btn-outline-secondary" id="copyResponseBtn">
                    <i class="fas fa-copy me-2"></i>{{ __('Copy Response') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Environment Configuration Guide -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-flask me-2"></i>{{ __('Sandbox Environment') }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('Base URL') }}:</strong> <code>https://reexpaylimited.com</code></p>
                    <p><strong>{{ __('Environment Header') }}:</strong> <code>X-Environment: sandbox</code></p>
                    <p><strong>{{ __('Credentials') }}:</strong> {{ __('Use') }} <code>test_</code> {{ __('prefixed keys') }}</p>
                    <p><strong>{{ __('Purpose') }}:</strong> {{ __('Safe testing without real money') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-globe me-2"></i>{{ __('Production Environment') }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('Base URL') }}:</strong> <code>https://reexpaylimited.com</code></p>
                    <p><strong>{{ __('Environment Header') }}:</strong> <code>X-Environment: production</code></p>
                    <p><strong>{{ __('Credentials') }}:</strong> {{ __('No prefix for production keys') }}</p>
                    <p><strong>{{ __('Purpose') }}:</strong> {{ __('Live transactions with real money') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for Interactive Testing -->
@push('scripts')
    <script>
        "use strict";
        
        document.addEventListener('DOMContentLoaded', function() {
            const testEndpoint = document.getElementById('testEndpoint');
            const testEnvironment = document.getElementById('testEnvironment');
            const testApiBtn = document.getElementById('testApiBtn');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const responseSection = document.getElementById('responseSection');
            const initiatePaymentParams = document.getElementById('initiatePaymentParams');
            const verifyPaymentParams = document.getElementById('verifyPaymentParams');
            const copyResponseBtn = document.getElementById('copyResponseBtn');
            
            // Show/hide parameters based on endpoint selection
            testEndpoint.addEventListener('change', function() {
                if (this.value === 'initiate-payment') {
                    initiatePaymentParams.style.display = 'block';
                    verifyPaymentParams.style.display = 'none';
                } else {
                    initiatePaymentParams.style.display = 'none';
                    verifyPaymentParams.style.display = 'block';
                }
                responseSection.style.display = 'none';
            });
            
            // Generate random reference transaction
            document.getElementById('refTrx').value = 'TXN' + Math.random().toString(36).substr(2, 9).toUpperCase();
            
            // Test API button click handler
            testApiBtn.addEventListener('click', async function() {
                const endpoint = testEndpoint.value;
                const environment = testEnvironment.value;
                const apiKey = document.getElementById('testApiKey').value.trim();
                const merchantKey = document.getElementById('testMerchantKey').value.trim();
                
                // Validate required fields
                if (!apiKey || !merchantKey) {
                    alert('{{ __('Please enter both API Key and Merchant Key') }}');
                    return;
                }
                
                // Show loading
                loadingIndicator.style.display = 'block';
                responseSection.style.display = 'none';
                testApiBtn.disabled = true;
                
                try {
                    let response;
                    const startTime = Date.now();
                    
                    if (endpoint === 'initiate-payment') {
                        response = await testInitiatePayment(environment, apiKey, merchantKey);
                    } else {
                        response = await testVerifyPayment(environment, apiKey, merchantKey);
                    }
                    
                    const endTime = Date.now();
                    const responseTime = endTime - startTime;
                    
                    displayResponse(response, responseTime);
                } catch (error) {
                    displayError(error);
                } finally {
                    loadingIndicator.style.display = 'none';
                    testApiBtn.disabled = false;
                }
            });
            
            // Test initiate payment endpoint
            async function testInitiatePayment(environment, apiKey, merchantKey) {
                const paymentAmount = parseFloat(document.getElementById('paymentAmount').value);
                const currencyCode = document.getElementById('currencyCode').value;
                const refTrx = document.getElementById('refTrx').value.trim();
                const description = document.getElementById('description').value.trim();
                const successRedirect = document.getElementById('successRedirect').value.trim();
                const failureUrl = document.getElementById('failureUrl').value.trim();
                const cancelRedirect = document.getElementById('cancelRedirect').value.trim();
                const ipnUrl = document.getElementById('ipnUrl').value.trim();
                const customerName = document.getElementById('customerName').value.trim();
                const customerEmail = document.getElementById('customerEmail').value.trim();
                
                // Validate required fields
                if (!paymentAmount || !currencyCode || !refTrx || !successRedirect || !failureUrl) {
                    throw new Error('{{ __('Please fill in all required fields (marked with *)') }}');
                }
                
                const requestBody = {
                    payment_amount: paymentAmount,
                    currency_code: currencyCode,
                    ref_trx: refTrx,
                    success_redirect: successRedirect,
                    failure_url: failureUrl
                };
                
                // Add optional fields if provided
                if (description) requestBody.description = description;
                if (cancelRedirect) requestBody.cancel_redirect = cancelRedirect;
                if (ipnUrl) requestBody.ipn_url = ipnUrl;
                if (customerName) requestBody.customer_name = customerName;
                if (customerEmail) requestBody.customer_email = customerEmail;
                
                const response = await fetch('{{ url('/api/v1/initiate-payment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Environment': environment,
                        'X-API-Key': apiKey,
                        'X-Merchant-Key': merchantKey,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(requestBody)
                });
                
                return await handleResponse(response);
            }
            
            // Test verify payment endpoint
            async function testVerifyPayment(environment, apiKey, merchantKey) {
                const transactionId = document.getElementById('transactionId').value.trim();
                
                if (!transactionId) {
                    throw new Error('{{ __('Please enter a transaction ID') }}');
                }
                
                const response = await fetch(`{{ url('/api/v1/verify-payment') }}/${encodeURIComponent(transactionId)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Environment': environment,
                        'X-API-Key': apiKey,
                        'X-Merchant-Key': merchantKey,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                return await handleResponse(response);
            }
            
            // Handle API response with proper error detection
            async function handleResponse(response) {
                const responseText = await response.text();
                let responseBody;
                
                // Try to parse as JSON, fallback to text if it fails
                try {
                    responseBody = JSON.parse(responseText);
                } catch (e) {
                    // If response is HTML (error page), extract meaningful error
                    if (responseText.includes('<!DOCTYPE') || responseText.includes('<html')) {
                        // Try to extract error message from HTML
                        const titleMatch = responseText.match(/<title[^>]*>([^<]+)<\/title>/i);
                        const errorTitle = titleMatch ? titleMatch[1] : '{{ __('HTML Error Page Returned') }}';
                        
                        responseBody = {
                            error: true,
                            message: '{{ __('Server returned HTML instead of JSON. This usually indicates an authentication or routing issue.') }}',
                            html_error: errorTitle,
                            status_code: response.status,
                            troubleshooting: [
                                '{{ __('Check if your API credentials are correct') }}',
                                '{{ __('Verify the environment (sandbox/production)') }}',
                                '{{ __('Ensure the API endpoint is accessible') }}',
                                '{{ __('Check if CORS is properly configured') }}'
                            ]
                        };
                    } else {
                        responseBody = {
                            error: true,
                            message: '{{ __('Invalid response format') }}',
                            raw_response: responseText.substring(0, 500) + (responseText.length > 500 ? '...' : '')
                        };
                    }
                }
                
                return {
                    status: response.status,
                    statusText: response.statusText,
                    headers: Object.fromEntries(response.headers.entries()),
                    body: responseBody
                };
            }
            
            // Display API response
            function displayResponse(response, responseTime) {
                responseSection.style.display = 'block';
                
                // Update status badge
                const statusBadge = document.getElementById('statusBadge');
                const statusClass = response.status >= 200 && response.status < 300 ? 'bg-success' : 'bg-danger';
                statusBadge.className = `badge ${statusClass}`;
                statusBadge.textContent = `${response.status} ${response.statusText}`;
                
                // Update response time
                document.getElementById('responseTime').textContent = `(${responseTime}ms)`;
                
                // Update response headers
                document.getElementById('responseHeaders').textContent = JSON.stringify(response.headers, null, 2);
                
                // Update response body with better formatting
                const responseBodyElement = document.getElementById('responseBody');
                if (response.body && response.body.error && response.body.troubleshooting) {
                    // Special formatting for troubleshooting errors
                    responseBodyElement.innerHTML = `<span class="text-warning">⚠️ {{ __('API Testing Error') }}</span>\n\n${JSON.stringify(response.body, null, 2)}`;
                } else {
                    responseBodyElement.textContent = JSON.stringify(response.body, null, 2);
                }
                
                // Scroll to response section
                responseSection.scrollIntoView({ behavior: 'smooth' });
            }
            
            // Display client-side error
            function displayError(error) {
                responseSection.style.display = 'block';
                
                const statusBadge = document.getElementById('statusBadge');
                statusBadge.className = 'badge bg-danger';
                statusBadge.textContent = '{{ __('Client Error') }}';
                
                document.getElementById('responseTime').textContent = '';
                document.getElementById('responseHeaders').textContent = '{{ __('No headers available') }}';
                document.getElementById('responseBody').textContent = JSON.stringify({
                    error: true,
                    type: 'client_error',
                    message: error.message,
                    troubleshooting: [
                        '{{ __('Check your internet connection') }}',
                        '{{ __('Verify API credentials format') }}',
                        '{{ __('Ensure all required fields are filled') }}',
                        '{{ __('Try refreshing the page') }}'
                    ]
                }, null, 2);
                
                responseSection.scrollIntoView({ behavior: 'smooth' });
            }
            
            // Copy response to clipboard
            copyResponseBtn.addEventListener('click', function() {
                const responseBody = document.getElementById('responseBody').textContent;
                navigator.clipboard.writeText(responseBody).then(function() {
                    const originalText = copyResponseBtn.innerHTML;
                    copyResponseBtn.innerHTML = '<i class="fas fa-check me-2"></i>{{ __('Copied!') }}';
                    copyResponseBtn.classList.add('btn-success');
                    copyResponseBtn.classList.remove('btn-outline-secondary');
                    
                    setTimeout(function() {
                        copyResponseBtn.innerHTML = originalText;
                        copyResponseBtn.classList.remove('btn-success');
                        copyResponseBtn.classList.add('btn-outline-secondary');
                    }, 2000);
                });
            });
        });
    </script>
@endpush
