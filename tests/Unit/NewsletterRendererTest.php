<?php

namespace Tests\Unit;

use App\Models\Newsletter;
use App\Services\Newsletter\NewsletterRenderer;
use Tests\TestCase;

class NewsletterRendererTest extends TestCase
{
    public function test_it_personalizes_content_and_always_adds_unsubscribe_links(): void
    {
        $newsletter = new Newsletter([
            'content_html' => '<h1>Hallo {voornaam}</h1><p>{email}</p>',
            'content_text' => 'Hallo {voornaam}',
        ]);

        $rendered = app(NewsletterRenderer::class)->renderForRecipient($newsletter, [
            'name' => 'Jan de Vries',
            'first_name' => 'Jan',
            'email' => 'jan@example.com',
        ]);

        $this->assertStringContainsString('Hallo Jan', $rendered['html']);
        $this->assertStringContainsString('jan@example.com', $rendered['html']);
        $this->assertStringContainsString('newsletter/unsubscribe', $rendered['html']);
        $this->assertStringContainsString('newsletter/unsubscribe', $rendered['text']);
    }

    public function test_it_does_not_duplicate_an_explicit_unsubscribe_link(): void
    {
        $newsletter = new Newsletter([
            'content_html' => '<a href="{unsubscribe_url}">Uitschrijven</a>',
            'content_text' => 'Uitschrijven: {unsubscribe_url}',
        ]);

        $rendered = app(NewsletterRenderer::class)->renderForRecipient($newsletter, [
            'name' => 'Jan de Vries',
            'first_name' => 'Jan',
            'email' => 'jan@example.com',
        ]);

        $this->assertSame(1, substr_count($rendered['html'], 'newsletter/unsubscribe'));
        $this->assertSame(1, substr_count($rendered['text'], 'newsletter/unsubscribe'));
    }
}
