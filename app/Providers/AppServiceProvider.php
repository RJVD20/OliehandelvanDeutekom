<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;
use Mollie\Api\MollieApiClient;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MollieApiClient::class, function (): MollieApiClient {
            $apiKey = config('payments.provider_options.mollie.api_key');

            if (! is_string($apiKey) || $apiKey === '') {
                throw new RuntimeException('MOLLIE_API_KEY is niet geconfigureerd.');
            }

            return (new MollieApiClient)->setApiKey($apiKey);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Bevestig je e-mailadres')
                ->view('emails.auth-action', [
                    'eyebrow' => 'Welkom bij ons',
                    'title' => 'Bevestig je e-mailadres',
                    'name' => $notifiable->name,
                    'lines' => [
                        'Bedankt voor het aanmaken van je account bij Kachels & Vloeistoffen.',
                        'Klik op de onderstaande knop om je e-mailadres te bevestigen. Daarna is je account klaar voor gebruik.',
                    ],
                    'actionText' => 'E-mailadres bevestigen',
                    'actionUrl' => $url,
                    'footer' => 'Heb je geen account aangemaakt? Dan kun je deze e-mail veilig negeren.',
                ]);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->subject('Wachtwoord opnieuw instellen')
                ->view('emails.auth-action', [
                    'eyebrow' => 'Accountbeveiliging',
                    'title' => 'Kies een nieuw wachtwoord',
                    'name' => $notifiable->name,
                    'lines' => [
                        'We hebben een verzoek ontvangen om het wachtwoord van je account opnieuw in te stellen.',
                        'Gebruik de onderstaande knop om een nieuw wachtwoord te kiezen. De link is 60 minuten geldig.',
                    ],
                    'actionText' => 'Nieuw wachtwoord instellen',
                    'actionUrl' => $url,
                    'footer' => 'Heb je dit niet aangevraagd? Dan hoef je niets te doen en blijft je huidige wachtwoord geldig.',
                ]);
        });
    }
}
