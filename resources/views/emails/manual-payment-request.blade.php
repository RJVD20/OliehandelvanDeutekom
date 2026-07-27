@extends('emails.layout')

@section('title', 'Betaalverzoek bestelling #' . $payment->order_id)

@section('content')
<h1 style="margin:0 0 6px 0;font-size:22px;font-weight:700;color:#10263D;letter-spacing:-0.02em;">Betaalverzoek</h1>
<p style="margin:0 0 24px 0;font-size:15px;color:#6B7280;line-height:1.7;">
    Beste {{ $payment->order->name }}, hieronder vind je de betaallink voor bestelling #{{ $payment->order_id }}.
</p>

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:24px;">
    <tr>
        <td style="background-color:#F7F8FA;border-left:3px solid #D9A42E;border-radius:0 8px 8px 0;padding:16px 18px;">
            <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6B7280;">Openstaand bedrag</p>
            <p style="margin:5px 0 0;font-size:22px;font-weight:700;color:#10263D;">€ {{ number_format($payment->amount, 2, ',', '.') }}</p>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
    <tr>
        <td align="center">
            <a href="{{ $payment->pay_link }}"
               style="display:inline-block;background-color:#D9A42E;color:#03182B;font-size:15px;font-weight:700;text-decoration:none;padding:14px 36px;border-radius:8px;">
                Veilig betalen →
            </a>
        </td>
    </tr>
</table>

<p style="margin:0;font-size:14px;color:#6B7280;line-height:1.7;">
    Heb je inmiddels betaald, dan kun je dit bericht negeren. Neem bij vragen gerust contact met ons op.
</p>

<p style="margin:24px 0 0;font-size:14px;color:#6B7280;line-height:1.7;">
    Met vriendelijke groet,<br>
    <strong style="color:#10263D;">Kachels &amp; Vloeistoffen</strong>
</p>
@endsection
