<?php

namespace App\Services\Newsletter;

use App\Models\Newsletter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class NewsletterRenderer
{
    /**
     * Render HTML and text versions for a recipient.
     */
    public function renderForRecipient(Newsletter $newsletter, array $recipient): array
    {
        $html = $this->replacePlaceholders($newsletter->content_html, $recipient);
        $textSource = $newsletter->content_text ?: strip_tags($html);
        $text = $this->replacePlaceholders($textSource, $recipient);

        return [
            'html' => $html,
            'text' => $text,
        ];
    }

    protected function replacePlaceholders(string $content, array $recipient): string
    {
        $replacements = [
            '{voornaam}' => $recipient['first_name'] ?? $recipient['name'] ?? '',
            '{naam}' => $recipient['name'] ?? '',
            '{email}' => $recipient['email'] ?? '',
            '{unsubscribe_url}' => $this->unsubscribeUrl($recipient),
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
}
