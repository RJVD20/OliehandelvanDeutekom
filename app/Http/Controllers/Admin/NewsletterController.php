<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\NewsletterDispatchJob;
use App\Models\AuditLog;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterUnsubscribe;
use App\Services\Newsletter\NewsletterRecipientResolver;
use App\Services\Newsletter\NewsletterRenderer;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function index(Request $request, NewsletterRecipientResolver $resolver): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:draft,scheduled,sending,sent,failed'],
        ]);

        $newsletters = Newsletter::query()
            ->withCount([
                'sends as sent_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_SENT),
                'sends as failed_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_FAILED),
            ])
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(
                fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('subject', 'like', "%{$search}%")
            ))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15);

        $stats = [
            'drafts' => Newsletter::where('status', Newsletter::STATUS_DRAFT)->count(),
            'scheduled' => Newsletter::where('status', Newsletter::STATUS_SCHEDULED)->count(),
            'sent_this_month' => Newsletter::where('status', Newsletter::STATUS_SENT)
                ->whereBetween('sent_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'eligible_recipients' => $resolver->count('all_users'),
            'unsubscribed' => NewsletterUnsubscribe::count(),
        ];

        return view('admin.newsletters.index', compact('newsletters', 'filters', 'stats'));
    }

    public function create(NewsletterRecipientResolver $resolver): View
    {
        return view('admin.newsletters.form', [
            'newsletter' => new Newsletter(['status' => Newsletter::STATUS_DRAFT]),
            'mode' => 'create',
            'provinces' => nl_provinces(),
            'audienceCount' => $resolver->count('all_users'),
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
        AuditLog::record('created', 'newsletter', $newsletter->id, $newsletter->title, [], $newsletter->toArray());

        return Redirect::route('admin.newsletters.edit', $newsletter)->with('toast', 'Nieuwsbrief opgeslagen als concept.');
    }

    public function edit(Newsletter $newsletter, NewsletterRecipientResolver $resolver): View
    {
        return view('admin.newsletters.form', [
            'newsletter' => $newsletter,
            'mode' => 'edit',
            'provinces' => nl_provinces(),
            'audienceCount' => $resolver->queryForNewsletter($newsletter)->count(),
        ]);
    }

    public function update(Request $request, Newsletter $newsletter): RedirectResponse
    {
        $before = $newsletter->toArray();
        $newsletter->update(array_merge($this->validatedData($request), [
            'updated_by' => auth()->id(),
        ]));
        AuditLog::record('updated', 'newsletter', $newsletter->id, $newsletter->title, $before, $newsletter->fresh()->toArray());

        return Redirect::route('admin.newsletters.edit', $newsletter)->with('toast', 'Nieuwsbrief opgeslagen.');
    }

    public function show(Newsletter $newsletter, NewsletterRecipientResolver $resolver): View
    {
        $newsletter->loadCount([
            'sends as sent_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_SENT),
            'sends as failed_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_FAILED),
            'sends as queued_count' => fn ($q) => $q->where('status', NewsletterSend::STATUS_QUEUED),
        ]);

        $sends = $newsletter->sends()->latest()->paginate(20);

        $audienceCount = $resolver->queryForNewsletter($newsletter)->count();

        return view('admin.newsletters.show', compact('newsletter', 'sends', 'audienceCount'));
    }

    public function send(Request $request, Newsletter $newsletter): RedirectResponse
    {
        $request->validate(['confirm_send' => ['accepted']]);
        if (! in_array($newsletter->status, [Newsletter::STATUS_DRAFT, Newsletter::STATUS_SCHEDULED], true)) {
            return Redirect::back()->with('toast', 'Kan niet verzenden in huidige status.');
        }

        $previousStatus = $newsletter->status;
        $newsletter->update([
            'status' => Newsletter::STATUS_SENDING,
            'scheduled_at' => null,
            'send_lock_at' => null,
        ]);

        NewsletterDispatchJob::dispatch($newsletter->id);
        AuditLog::record('updated', 'newsletter', $newsletter->id, $newsletter->title, ['status' => $previousStatus], ['status' => Newsletter::STATUS_SENDING]);

        return Redirect::route('admin.newsletters.index')->with('toast', 'Verzending gestart via queue.');
    }

    public function schedule(Request $request, Newsletter $newsletter): RedirectResponse
    {
        $request->validate([
            'scheduled_at' => ['required', 'date_format:Y-m-d\TH:i'],
        ]);

        $scheduledAt = CarbonImmutable::createFromFormat(
            'Y-m-d\TH:i',
            $request->string('scheduled_at')->toString(),
            config('newsletter.timezone')
        );

        if ($scheduledAt->isPast()) {
            throw ValidationException::withMessages([
                'scheduled_at' => 'Kies een tijdstip in de toekomst.',
            ]);
        }

        $previousStatus = $newsletter->status;
        $newsletter->update([
            'status' => Newsletter::STATUS_SCHEDULED,
            'scheduled_at' => $scheduledAt->utc(),
            'send_lock_at' => null,
        ]);
        AuditLog::record('updated', 'newsletter', $newsletter->id, $newsletter->title, ['status' => $previousStatus], [
            'status' => Newsletter::STATUS_SCHEDULED,
            'scheduled_at' => $newsletter->scheduled_at?->toDateTimeString(),
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
        AuditLog::record('updated', 'newsletter', $newsletter->id, $newsletter->title, ['status' => Newsletter::STATUS_SCHEDULED], ['status' => Newsletter::STATUS_DRAFT]);

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
        AuditLog::record('created', 'newsletter', $copy->id, $copy->title, [], $copy->toArray());

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
        AuditLog::record('updated', 'newsletter', $newsletter->id, $newsletter->title, ['testmail' => null], ['testmail' => $recipient['email']]);

        return Redirect::back()->with('toast', 'Testmail verstuurd.');
    }

    protected function validatedData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'content_html' => ['required', 'string'],
            'content_text' => ['nullable', 'string'],
            'target_audience' => ['required', 'in:all_users,customers,province,fulfillment,recent_customers'],
            'audience_province' => ['nullable', 'required_if:target_audience,province', 'in:'.implode(',', nl_provinces())],
            'audience_fulfillment' => ['nullable', 'required_if:target_audience,fulfillment', 'in:delivery,pickup'],
            'audience_ordered_since' => ['nullable', 'required_if:target_audience,recent_customers', 'date'],
        ]);

        $data['filters'] = [
            'province' => $data['audience_province'] ?? null,
            'fulfillment_method' => $data['audience_fulfillment'] ?? null,
            'ordered_since' => $data['audience_ordered_since'] ?? null,
        ];
        unset($data['audience_province'], $data['audience_fulfillment'], $data['audience_ordered_since']);

        return $data;
    }

    public function audienceCount(Request $request, NewsletterRecipientResolver $resolver): JsonResponse
    {
        $data = $request->validate([
            'target_audience' => ['required', 'in:all_users,customers,province,fulfillment,recent_customers'],
            'province' => ['nullable', 'in:'.implode(',', nl_provinces())],
            'fulfillment_method' => ['nullable', 'in:delivery,pickup'],
            'ordered_since' => ['nullable', 'date'],
        ]);

        return response()->json([
            'count' => $resolver->count($data['target_audience'], [
                'province' => $data['province'] ?? null,
                'fulfillment_method' => $data['fulfillment_method'] ?? null,
                'ordered_since' => $data['ordered_since'] ?? null,
            ]),
        ]);
    }
}
