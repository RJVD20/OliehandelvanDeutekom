<?php

namespace App\Http\Controllers;

use App\Models\NewsletterUnsubscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NewsletterUnsubscribeController
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        NewsletterUnsubscribe::updateOrCreate(
            ['email' => $request->input('email')],
            [
                'user_id' => auth()->id(),
                'unsubscribed_at' => now(),
            ]
        );

        return Redirect::route('home')->with('toast', 'Je bent uitgeschreven voor de nieuwsbrief.');
    }
}
