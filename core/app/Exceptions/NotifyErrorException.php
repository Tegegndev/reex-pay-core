<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;

class NotifyErrorException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): RedirectResponse
    {
        // Use a notification service or session flashing
        notifyEvs('error', $this->getMessage());

        // Redirect back with the notification
        return redirect()->back();
    }
}
