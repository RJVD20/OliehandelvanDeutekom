@extends('emails.layout')

@section('title', 'Je bestelling wordt bezorgd')

@section('content')

{{-- Greeting --}}
<h1 style="margin:0 0 6px 0;font-size:22px;font-weight:700;color:#10263D;letter-spacing:-0.02em;">Je bestelling is onderweg!</h1>
<p style="margin:0 0 24px 0;font-size:15px;color:#6B7280;">
    Beste {{ $order->name }}, goed nieuws — je bestelling wordt morgen bij je bezorgd.
</p>

{{-- Status banner --}}
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
    <tr>
        <td style="background-color:#03182B;border-radius:8px;padding:20px 24px;text-align:center;">
            <p style="margin:0 0 4px 0;font-size:28px;">🚚</p>
            <p style="margin:0 0 2px 0;font-size:16px;font-weight:700;color:#ffffff;">Bestelling #{{ $order->id }}</p>
            <p style="margin:0;font-size:13px;color:#D9A42E;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;">Onderweg naar jou</p>
        </td>
    </tr>
</table>

{{-- Delivery address --}}
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
    <tr>
        <td style="background-color:#F7F8FA;border-left:3px solid #D9A42E;border-radius:0 8px 8px 0;padding:16px 18px;">
            <p style="margin:0 0 6px 0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6B7280;">Bezorgadres</p>
            <p style="margin:0;font-size:14px;color:#10263D;line-height:1.7;">
                {{ $order->name }}<br>
                {{ $order->address }}<br>
                {{ $order->postcode }} {{ $order->city }}
            </p>
        </td>
    </tr>
</table>

{{-- Order summary --}}
<p style="margin:0 0 10px 0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#10263D;">Jouw bestelling</p>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border:1px solid #E9EDF2;border-radius:8px;overflow:hidden;margin-bottom:28px;">
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td style="padding:12px 14px;font-size:14px;color:#10263D;border-bottom:1px solid #E9EDF2;">{{ $item->product_name }}</td>
            <td style="padding:12px 14px;font-size:14px;color:#6B7280;text-align:center;border-bottom:1px solid #E9EDF2;">{{ $item->quantity }}×</td>
            <td style="padding:12px 14px;font-size:14px;font-weight:600;color:#10263D;text-align:right;border-bottom:1px solid #E9EDF2;">€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="margin:0 0 6px 0;font-size:14px;color:#6B7280;line-height:1.7;">
    Heb je vragen over je bezorging? Neem gerust contact met ons op via
    <a href="mailto:info@kachelvloeistof.nl" style="color:#D9A42E;text-decoration:none;">info@kachelvloeistof.nl</a>.
</p>

<p style="margin:24px 0 0 0;font-size:14px;color:#6B7280;line-height:1.7;">
    Met vriendelijke groet,<br>
    <strong style="color:#10263D;">Kachelvloeistof.nl</strong>
</p>

@endsection
