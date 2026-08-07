<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if ($user->hasVerifiedEmail()) {
            return $this->redirectAfterVerification(
                $request,
                'Je e-mailadres was al bevestigd. Je account is klaar voor gebruik.'
            );
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectAfterVerification(
            $request,
            'Gelukt! Je e-mailadres is bevestigd en je account is nu actief.'
        );
    }

    private function redirectAfterVerification(Request $request, string $message): RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('profile.edit')->with('toast', $message);
        }

        return redirect()->route('login')->with('status', $message.' Je kunt nu inloggen.');
    }
}
