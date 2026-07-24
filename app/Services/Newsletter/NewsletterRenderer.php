<?php

namespace App\Services\Newsletter;

use App\Models\Newsletter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class NewsletterRenderer
{
    /**
     * Render HTML and text versions for a recipient.
     */
    public function renderForRecipient(Newsletter $newsletter, array $recipient): array
    {
        $unsubscribeUrl = $this->unsubscribeUrl($recipient);
        $htmlHasUnsubscribeLink = Str::contains($newsletter->content_html, '{unsubscribe_url}');
        $html = $this->replacePlaceholders($newsletter->content_html, $recipient, $unsubscribeUrl);

        if (! $htmlHasUnsubscribeLink) {
            $html .= $this->htmlUnsubscribeFooter($unsubscribeUrl);
        }

        $textSource = $newsletter->content_text ?: strip_tags($newsletter->content_html);
        $textHasUnsubscribeLink = Str::contains($textSource, '{unsubscribe_url}');
        $text = $this->replacePlaceholders($textSource, $recipient, $unsubscribeUrl);

        if (! $textHasUnsubscribeLink) {
            $text .= "\n\nUitschrijven voor deze nieuwsbrief: {$unsubscribeUrl}";
        }

        return [
            'html' => $html,
            'text' => $text,
        ];
    }

    protected function replacePlaceholders(string $content, array $recipient, string $unsubscribeUrl): string
    {
        $replacements = [
            '{voornaam}' => $recipient['first_name'] ?? $recipient['name'] ?? '',
            '{naam}' => $recipient['name'] ?? '',
            '{email}' => $recipient['email'] ?? '',
            '{unsubscribe_url}' => $unsubscribeUrl,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    protected function unsubscribeUrl(array $recipient): string
    {
        $email = $recipient['email'] ?? '';
        $name = $recipient['name'] ?? null;

        return URL::signedRoute('newsletter.unsubscribe', [
            'email' => $email,
            'name' => $name,
        ]);
    }

    protected function htmlUnsubscribeFooter(string $unsubscribeUrl): string
    {
        $url = e($unsubscribeUrl);

        return <<<HTML
            <p style="margin-top: 32px; padding-top: 16px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 12px;">
                Je ontvangt deze e-mail omdat je bij ons bekend bent.
                <a href="{$url}" style="color: #4b5563;">Uitschrijven voor de nieuwsbrief</a>.
            </p>
        HTML;
    }
}
