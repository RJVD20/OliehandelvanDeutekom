<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('profile.edit')
                ->with('toast', 'Je e-mailadres is al bevestigd.');
        }

        $user = $request->user();

        // SMTP can occasionally take several seconds. Send only after the response
        // has reached the browser, so the verification page never keeps spinning.
        app()->terminating(function () use ($user): void {
            try {
                $user->sendEmailVerificationNotification();
            } catch (Throwable $exception) {
                Log::error('Verification email could not be sent.', [
                    'user_id' => $user->getKey(),
                    'exception' => $exception,
                ]);
            }
        });

        return back()->with('status', 'verification-link-sent');
    }
}
