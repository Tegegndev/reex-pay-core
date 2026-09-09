<!-- Webhooks Section -->
<section id="webhooks" class="content-section">
    <h2>{{ __('Webhooks (IPN)') }}</h2>
    <p>{{ __('REEXPAY sends real-time notifications to your specified IPN URL when payment status changes. This ensures you\'re immediately notified of payment completions, failures, and other status updates.') }} <strong>{{ __('Webhooks work identically in both sandbox and production environments.') }}</strong></p>

    <!-- Environment Notice -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-3"></i>
            <div>
                <h6 class="mb-1">{{ __('Environment-Aware Webhooks') }}</h6>
                <p class="mb-0">{{ __('Use the same webhook URL for both sandbox and production. REEXPAY will include environment context in webhook payloads to help you differentiate between test and live transactions.') }}</p>
            </div>
        </div>
    </div>

    <!-- Webhook Overview -->
    <div class="api-alert api-alert-info">
        <strong><i class="fas fa-info-circle me-2"></i>{{ __('Reliable Delivery') }}</strong>
        {{ __('REEXPAY implements retry logic for failed webhook deliveries. We\'ll retry up to 5 times with exponential backoff.') }}
    </div>

    <!-- Webhook Headers -->
    <h3>{{ __('Webhook Headers') }}</h3>
    <table class="api-table">
        <thead>
            <tr>
                <th>{{ __('Header') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Example') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>Content-Type</code></td>
                <td>{{ __('Always') }} <code>application/json</code></td>
                <td><code>application/json</code></td>
            </tr>
            <tr>
                <td><code>X-Signature</code></td>
                <td>{{ __('HMAC-SHA256 signature for verification') }}</td>
                <td><code>a8b9c2d1e5f3...</code></td>
            </tr>
        </tbody>
    </table>

    <!-- Webhook Payload Structure -->
    <h3>{{ __('Webhook Payload') }}</h3>
    <p>{{ __('All webhook payloads include environment information to help you differentiate between sandbox and production transactions') }}:</p>
    
    <div class="alert alert-warning mb-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>{{ __('Environment Context:') }} </strong> <code>environment</code> {{ __('field will be') }} <code>sandbox</code> {{ __('for test transactions or') }} <code>production</code> {{ __('for live transactions. Transaction IDs are prefixed accordingly') }} (<code>SANDBOX_</code> {{ __('or') }} <code>PRODUCTION_</code>).
    </div>
    
    <div class="code-block">
        <pre><code class="json">{
    "data": {
        "ref_trx": "TXNT4AQFESTAG4F",
        "description": "Order #1234",
        "ipn_url": "https://webhook.site/5711b7d5-917a-4d94-bbb3-c28f4a37bea5",
        "cancel_redirect": "https://merchant.com/cancel",
        "success_redirect": "https://merchant.com/success",
        "customer_name": "John Doe",
        "customer_email": "john@example.com",
        "merchant_name": "Xanthus Wiggins",
        "amount": 200,
        "currency_code": "USD",
        "environment": "production",
        "is_sandbox": false
    },
    "message": "{{ __('Payment Completed') }}",
    "status": "{{ __('completed') }}",
    "timestamp": 1705747245
}</code></pre>
    </div>

    <!-- Signature Verification -->
    <h3>{{ __('Signature Verification') }}</h3>
    <p>{{ __('Always verify webhook signatures to ensure authenticity and prevent unauthorized requests. Use your API secret (environment-specific) to verify signatures') }}:</p>

    <!-- Environment Note -->
    <div class="alert alert-warning mb-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>{{ __('Environment-Specific Secrets:') }} </strong> {{ __('Use') }} <code>test_webhook_secret</code> {{ __('for sandbox and') }} <code>webhook_secret</code> {{ __('for production environments.') }}
    </div>

    <div class="language-tabs">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#webhook-php">
                    <i class="fa-brands fa-php text-primary me-1" aria-hidden="true"></i>{{ __('PHP (Laravel)') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#webhook-nodejs">
                    <i class="fa-brands fa-node-js text-success me-1" aria-hidden="true"></i>{{ __('Node.js') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#webhook-python">
                    <i class="fa-brands fa-python text-primary me-1" aria-hidden="true"></i>{{ __('Python') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="webhook-php">
                <div class="code-block">
                    <pre><code class="php">&lt;?php
// {{ __('Laravel Webhook Handler') }}
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Enums\EnvironmentMode;

class REEXPAYWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // {{ __('Get webhook headers') }}
        $environment = $request->header('X-Environment', 'production');
        $signature = $request->header('X-Signature');
        $webhookId = $request->header('X-Webhook-ID');
        
        // {{ __('Get appropriate secret based on environment') }}
        $secret = $this->getSecretForEnvironment($environment);
        
        // {{ __('Verify signature') }}
        if (!$this->verifySignature($request->getContent(), $signature, $secret)) {
            Log::warning('{{ __('REEXPAY webhook signature verification failed') }}', [
                'webhook_id' => $webhookId,
                'environment' => $environment
            ]);
            
            return response()->json(['error' => '{{ __('Invalid signature') }}'], 401);
        }
        
        $payload = $request->json()->all();
        
        // {{ __('Handle based on environment') }}
        if ($environment === EnvironmentMode::SANDBOX->value) {
            return $this->handleSandboxWebhook($payload);
        } else {
            return $this->handleProductionWebhook($payload);
        }
    }
    
    private function getSecretForEnvironment(string $environment): string
    {
        // {{ __('Return test secret for sandbox, live secret for production') }}
        return $environment === 'sandbox' 
            ? config('reexpaylimited.com_webhook_secret')
            : config('reexpay.webhook_secret');
    }
    
    private function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
    
    private function handleSandboxWebhook(array $payload): JsonResponse
    {
        Log::info('{{ __('Processing sandbox webhook') }}', $payload);
        
        // {{ __('Your sandbox-specific logic here') }}
        // {{ __('Don\'t fulfill orders, don\'t send emails to real customers, etc.') }}
        
        return response()->json(['status' => '{{ __('sandbox_processed') }}']);
    }
    
    private function handleProductionWebhook(array $payload): JsonResponse
    {
        Log::info('{{ __('Processing production webhook') }}', $payload);
        
        // {{ __('Your production logic here') }}
        // {{ __('Fulfill orders, send confirmation emails, etc.') }}
        
        return response()->json(['status' => '{{ __('processed') }}']);
    }
}</code></pre>
                </div>
            </div>
            
            <div class="tab-pane fade" id="webhook-nodejs">
                <div class="code-block">
                    <pre><code class="javascript">const crypto = require('crypto');
const express = require('express');

const EnvironmentMode = {
    SANDBOX: 'sandbox',
    PRODUCTION: 'production'
};

// {{ __('Webhook handler') }}
app.post('/api/ipn/marzpay', async (req, res) => {
    const environment = req.headers['x-environment'] || 'production';
    const signature = req.headers['x-signature'];
    const webhookId = req.headers['x-webhook-id'];
    
    // {{ __('Get appropriate secret based on environment') }}
    const secret = getSecretForEnvironment(environment);
    
    // {{ __('Verify signature') }}
    if (!verifySignature(JSON.stringify(req.body), signature, secret)) {
        console.warn('{{ __('REEXPAY webhook signature verification failed') }}', {
            webhook_id: webhookId,
            environment: environment
        });
        
        return res.status(401).json({ error: '{{ __('Invalid signature') }}' });
    }
    
    const payload = req.body;
    
    try {
        // {{ __('Handle based on environment') }}
        if (environment === EnvironmentMode.SANDBOX) {
            await handleSandboxWebhook(payload);
        } else {
            await handleProductionWebhook(payload);
        }
        
        res.json({ status: '{{ __('processed') }}' });
    } catch (error) {
        console.error('{{ __('Webhook processing error') }}:', error);
        res.status(500).json({ error: '{{ __('Processing failed') }}' });
    }
});

function getSecretForEnvironment(environment) {
    // {{ __('Return test secret for sandbox, live secret for production') }}
    return environment === 'sandbox' 
        ? process.env.REEXPAY_TEST_WEBHOOK_SECRET
        : process.env.REEXPAY_WEBHOOK_SECRET;
}

function verifySignature(payload, signature, secret) {
    if (!signature) {
        return false;
    }
    
    const expectedSignature = 'sha256=' + crypto
        .createHmac('sha256', secret)
        .update(payload)
        .digest('hex');
    
    return crypto.timingSafeEqual(
        Buffer.from(expectedSignature),
        Buffer.from(signature)
    );
}

async function handleSandboxWebhook(payload) {
    console.log('{{ __('Processing sandbox webhook') }}:', payload);
    
    // {{ __('Your sandbox-specific logic here') }}
    // {{ __('Don\'t fulfill orders, don\'t send emails to real customers, etc.') }}
}

async function handleProductionWebhook(payload) {
    console.log('{{ __('Processing production webhook') }}:', payload);
    
    // {{ __('Your production logic here') }}
    // {{ __('Fulfill orders, send confirmation emails, etc.') }}
}</code></pre>
                </div>
            </div>
            
            <div class="tab-pane fade" id="webhook-python">
                <div class="code-block">
                    <pre><code class="python">import hmac
import hashlib
import json
import logging
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.views.decorators.http import require_http_methods

logger = logging.getLogger(__name__)

ENVIRONMENT_MODE = {
    'SANDBOX': 'sandbox',
    'PRODUCTION': 'production'
}

@csrf_exempt
@require_http_methods(["POST"])
def reexpay_webhook(request):
    environment = request.headers.get('X-Environment', 'production')
    signature = request.headers.get('X-Signature', '')
    webhook_id = request.headers.get('X-Webhook-ID')
    
    // {{ __('Get appropriate secret based on environment') }}
    secret = get_secret_for_environment(environment)
    
    // {{ __('Verify signature') }}
    if not verify_signature(request.body, signature, secret):
        logger.warning('{{ __('REEXPAY webhook signature verification failed') }}', extra={
            'webhook_id': webhook_id,
            'environment': environment
        })
        
        return JsonResponse({'error': '{{ __('Invalid signature') }}'}, status=401)
    
    try:
        payload = json.loads(request.body)
        
        // {{ __('Handle based on environment') }}
        if environment == ENVIRONMENT_MODE['SANDBOX']:
            handle_sandbox_webhook(payload)
        else:
            handle_production_webhook(payload)
        
        return JsonResponse({'status': '{{ __('processed') }}'})
    
    except Exception as e:
        logger.error(f'{{ __('Webhook processing error') }}:{str(e)}')
        return JsonResponse({'error': '{{ __('Processing failed') }}'}, status=500)

def get_secret_for_environment(environment):
    from django.conf import settings
    
    // {{ __('Return test secret for sandbox, live secret for production') }}
    return (settings.REEXPAY_TEST_WEBHOOK_SECRET 
            if environment == 'sandbox' 
            else settings.REEXPAY_WEBHOOK_SECRET)

def verify_signature(payload, signature, secret):
    if not signature:
        return False
    
    expected_signature = 'sha256=' + hmac.new(
        secret.encode('utf-8'),
        payload,
        hashlib.sha256
    ).hexdigest()
    
    return hmac.compare_digest(expected_signature, signature)

def handle_sandbox_webhook(payload):
    logger.info('{{ __('Processing sandbox webhook') }}', extra=payload)
    
    // {{ __('Your sandbox-specific logic here') }}
    // {{ __('Don\'t fulfill orders, don\'t send emails to real customers, etc.') }}

def handle_production_webhook(payload):
    logger.info('{{ __('Processing production webhook') }}', extra=payload)
    
    // {{ __('Your production logic here') }}
    // {{ __('Fulfill orders, send confirmation emails, etc.') }}
</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Environment-Specific Best Practices -->
    <h3>{{ __('Environment-Specific Best Practices') }}</h3>
    <div class="row">
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-flask me-2"></i>{{ __('Sandbox Webhooks') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Use for testing webhook integration') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('No real money transactions') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Don\'t fulfill actual orders') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Don\'t send emails to real customers') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Use test webhook secret for verification') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-rocket me-2"></i>{{ __('Production Webhooks') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Process real customer orders') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Send confirmation emails') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Update inventory systems') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Trigger fulfillment processes') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>{{ __('Use production webhook secret for verification') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
