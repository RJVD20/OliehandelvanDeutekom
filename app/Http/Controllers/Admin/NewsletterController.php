<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\NewsletterDispatchJob;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterUnsubscribe;
use App\Services\Newsletter\NewsletterRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function index(): View
    {
        $newsletters = Newsletter::query()
            ->withCount([
                'sends as sent_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_SENT),
                'sends as failed_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_FAILED),
            ])
            ->latest()
            ->paginate(15);

        return view('admin.newsletters.index', compact('newsletters'));
    }

    public function create(): View
    {
        return view('admin.newsletters.form', [
            'newsletter' => new Newsletter(['status' => Newsletter::STATUS_DRAFT]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $newsletter = Newsletter::create(array_merge($data, [
            'status' => Newsletter::STATUS_DRAFT,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]));

        return Redirect::route('admin.newsletters.edit', $newsletter)->with('toast', 'Nieuwsbrief opgeslagen als concept.');
    }

    public function edit(Newsletter $newsletter): View
    {
        return view('admin.newsletters.form', [
            'newsletter' => $newsletter,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Newsletter $newsletter): RedirectResponse
    {
        $newsletter->update(array_merge($this->validatedData($request), [
            'updated_by' => auth()->id(),
        ]));

        return Redirect::route('admin.newsletters.edit', $newsletter)->with('toast', 'Nieuwsbrief opgeslagen.');
    }

    public function show(Newsletter $newsletter): View
    {
        $newsletter->loadCount([
            'sends as sent_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_SENT),
            'sends as failed_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_FAILED),
        ]);

        $sends = $newsletter->sends()->latest()->paginate(20);

        return view('admin.newsletters.show', compact('newsletter', 'sends'));
    }

    public function send(Request $request, Newsletter $newsletter): RedirectResponse
    {
        if (! in_array($newsletter->status, [Newsletter::STATUS_DRAFT, Newsletter::STATUS_SCHEDULED], true)) {
            return Redirect::back()->with('toast', 'Kan niet verzenden in huidige status.');
        }

        $newsletter->update([
            'status' => Newsletter::STATUS_SENDING,
            'scheduled_at' => null,
            'send_lock_at' => now(),
        ]);

        NewsletterDispatchJob::dispatch($newsletter->id);

        return Redirect::route('admin.newsletters.index')->with('toast', 'Verzending gestart via queue.');
    }

    public function schedule(Request $request, Newsletter $newsletter): RedirectResponse
    {
        $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        $newsletter->update([
            'status' => Newsletter::STATUS_SCHEDULED,
            'scheduled_at' => $request->scheduled_at,
            'send_lock_at' => null,
        ]);

        return Redirect::route('admin.newsletters.index')->with('toast', 'Nieuwsbrief ingepland.');
    }

    public function cancel(Newsletter $newsletter): RedirectResponse
    {
        if ($newsletter->status !== Newsletter::STATUS_SCHEDULED) {
            return Redirect::back()->with('toast', 'Alleen geplande nieuwsbrieven kunnen worden geannuleerd.');
        }

        $newsletter->update([
            'status' => Newsletter::STATUS_DRAFT,
            'scheduled_at' => null,
            'send_lock_at' => null,
        ]);

        return Redirect::route('admin.newsletters.edit', $newsletter)->with('toast', 'Ingeplande verzending geannuleerd.');
    }

    public function duplicate(Newsletter $newsletter): RedirectResponse
    {
        $copy = $newsletter->replicate([
            'status', 'scheduled_at', 'sent_at', 'send_lock_at',
        ]);
        $copy->status = Newsletter::STATUS_DRAFT;
        $copy->scheduled_at = null;
        $copy->sent_at = null;
        $copy->send_lock_at = null;
        $copy->created_by = auth()->id();
        $copy->updated_by = auth()->id();
        $copy->title = $newsletter->title.' (kopie)';
        $copy->save();

        return Redirect::route('admin.newsletters.edit', $copy)->with('toast', 'Nieuwsbrief gedupliceerd.');
    }

    public function test(Request $request, Newsletter $newsletter, NewsletterRenderer $renderer): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $recipient = [
            'email' => $request->email,
            'name' => auth()->user()->name ?? 'Test',
            'first_name' => Str::before(auth()->user()->name ?? 'Test', ' '),
        ];

        $rendered = $renderer->renderForRecipient($newsletter, $recipient);

        Mail::to($recipient['email'], $recipient['name'])
            ->send(new \App\Mail\NewsletterMailable($newsletter, $rendered['html'], $rendered['text']));

        return Redirect::back()->with('toast', 'Testmail verstuurd.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'content_html' => ['required', 'string'],
            'content_text' => ['nullable', 'string'],
        ]);
    }
}
