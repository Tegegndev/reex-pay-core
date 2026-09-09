<?php

namespace App\Http\Middleware;

use App\Exceptions\NotifyErrorException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class PreventDuplicateSubmission
{
    /**
     * Handle an incoming request and prevent duplicate submissions.
     *
     * Leverages Laravel cache locking to ensure only one identical request is processed at a time.
     * Uses a short-term cache marker to detect if an identical submission was recently completed.
     *
     * Supports both authenticated users and guests by including user/session identifiers in the lock key.
     * The duplicate submission window is configurable via `duplicate_submission_timeout` (in seconds).
     *
     * @throws HttpException|Throwable(429) if a duplicate submission is detected.
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to state-changing requests (POST, PUT, PATCH, DELETE).
        // Skip GET/HEAD requests as they are idempotent.
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            return $next($request);
        }

        // Determine a unique identifier for the user or session.
        // For logged-in users: use user ID; for guests: use session ID (fallback to IP if no session available).
        if ($request->user()) {
            $userTag = 'user:'.$request->user()->getAuthIdentifier();
        } elseif ($request->hasSession()) {
            $userTag = 'guest:'.$request->session()->getId();
        } else {
            $userTag = 'guest:'.$request->ip();
        }

        // Create a unique fingerprint for this request based on route and input data.
        // Exclude non-essential fields like CSRF token to avoid false mismatches.
        $route       = $request->path();
        $method      = $request->method();
        $payloadHash = sha1($route.'|'.$method.'|'.json_encode($request->except('_token')));

        // Compose cache keys for the lock and a submission marker.
        $lockKey   = "dup_submission_lock:{$userTag}:{$payloadHash}";
        $markerKey = "dup_submission_marker:{$userTag}:{$payloadHash}";

        // Get the duplicate submission timeout (in seconds) from config (default to 5 seconds if not set).
        $timeout = config('security.duplicate_submission_timeout', 5);

        // Attempt to acquire an atomic lock for this request.
        // This prevents parallel requests with the same key from executing simultaneously.
        $lock = Cache::lock($lockKey, $timeout);
        if (! $lock->get()) {
            // If we couldn't obtain the lock, a similar request is already in progress (or just finished within TTL).
            throw new NotifyErrorException(__('Duplicate submission detected. Please wait a moment before retrying.'));
        }

        try {
            // Check if an identical request was recently processed (within the timeout window).
            if (Cache::has($markerKey)) {
                // A recent identical submission exists, so abort this duplicate attempt.
                $lock->release();  // release the lock before aborting
                throw new NotifyErrorException(__('Duplicate submission detected. Please wait a moment before retrying.'));
            }

            // No recent duplicate found – proceed with the request.
            $response = $next($request);

            // After successful processing, set a marker in cache to flag this submission.
            // This marker lives for $timeout seconds to prevent back-to-back duplicates.
            Cache::put($markerKey, true, $timeout);

            return $response;
        } catch (Throwable $e) {
            // If an exception occurs, release the lock to avoid any deadlock and rethrow the error.
            $lock->release();
            throw $e;
        } finally {
            // Ensure the lock is released if still held (in normal flow).
            if ($lock->owner()) {
                $lock->release();
            }
        }
    }
}
