@extends('emails.layout')

@section('title', 'Orderbevestiging #' . $order->id)

@section('content')

{{-- Greeting --}}
<h1 style="margin:0 0 6px 0;font-size:22px;font-weight:700;color:#10263D;letter-spacing:-0.02em;">Bedankt voor je bestelling!</h1>
<p style="margin:0 0 24px 0;font-size:15px;color:#6B7280;">
    Beste {{ $order->name }}, we hebben je bestelling goed ontvangen en gaan er direct mee aan de slag.
</p>

{{-- Order badge --}}
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
    <tr>
        <td style="background-color:#F7F8FA;border-left:3px solid #D9A42E;border-radius:0 8px 8px 0;padding:14px 18px;">
            <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#D9A42E;">Bestelnummer</p>
            <p style="margin:4px 0 0 0;font-size:20px;font-weight:700;color:#10263D;">#{{ $order->id }}</p>
        </td>
    </tr>
</table>

{{-- Order items --}}
<p style="margin:0 0 10px 0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#10263D;">Bestelde producten</p>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border:1px solid #E9EDF2;border-radius:8px;overflow:hidden;margin-bottom:20px;">
    <thead>
        <tr style="background-color:#F7F8FA;">
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#6B7280;border-bottom:1px solid #E9EDF2;">Product</th>
            <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#6B7280;border-bottom:1px solid #E9EDF2;">Aantal</th>
            <th style="padding:10px 14px;text-align:right;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#6B7280;border-bottom:1px solid #E9EDF2;">Prijs</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td style="padding:12px 14px;font-size:14px;color:#10263D;border-bottom:1px solid #E9EDF2;">{{ $item->product_name }}</td>
            <td style="padding:12px 14px;font-size:14px;color:#10263D;text-align:center;border-bottom:1px solid #E9EDF2;">{{ $item->quantity }}</td>
            <td style="padding:12px 14px;font-size:14px;color:#10263D;text-align:right;border-bottom:1px solid #E9EDF2;">€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
        </tr>
        @endforeach
        @if((float) $order->shipping_cost > 0)
        <tr>
            <td colspan="2" style="padding:10px 14px;font-size:14px;color:#475569;text-align:right;">
                {{ $order->delivery_service === 'express' ? 'Express Premium / bezorging' : 'Bezorging' }}
            </td>
            <td style="padding:10px 14px;font-size:14px;color:#475569;text-align:right;">€ {{ number_format($order->shipping_cost, 2, ',', '.') }}</td>
        </tr>
        @endif
        <tr style="background-color:#F7F8FA;">
            <td colspan="2" style="padding:14px;font-size:14px;font-weight:700;color:#10263D;text-align:right;">Totaal</td>
            <td style="padding:14px;font-size:16px;font-weight:700;color:#D9A42E;text-align:right;">€ {{ number_format($order->total, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

{{-- Pay button --}}
@if($order->latestPayment?->pay_link)
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
    <tr>
        <td align="center">
            <a href="{{ $order->latestPayment->pay_link }}"
               style="display:inline-block;background-color:#D9A42E;color:#03182B;font-size:15px;font-weight:700;text-decoration:none;padding:14px 36px;border-radius:8px;letter-spacing:-0.01em;">
                Bestelling betalen →
            </a>
        </td>
    </tr>
</table>
@endif

{{-- Delivery or pickup --}}
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
    <tr>
        <td style="background-color:#F7F8FA;border-radius:8px;padding:16px 18px;">
            @if($order->fulfillment_method === 'pickup')
                <p style="margin:0 0 6px 0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6B7280;">Afhalen</p>
                <p style="margin:0;font-size:14px;color:#10263D;line-height:1.7;">
                    <strong>{{ $order->pickup_location_name ?: 'Gekozen depot' }}</strong>
                    @if($order->pickup_location_address)<br>{{ $order->pickup_location_address }}@endif
                    @if($order->pickup_location_opening)<br>Openingstijden: {{ $order->pickup_location_opening }}@endif
                </p>
            @else
                <p style="margin:0 0 6px 0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6B7280;">Afleveradres</p>
                <p style="margin:0;font-size:14px;color:#10263D;line-height:1.7;">
                    {{ $order->name }}<br>
                    {{ $order->address }}<br>
                    {{ $order->postcode }} {{ $order->city }}
                </p>
            @endif
        </td>
    </tr>
</table>

{{-- USPs --}}
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-top:1px solid #E9EDF2;padding-top:24px;margin-top:4px;">
    <tr>
        <td style="padding:0 8px 0 0;width:33%;vertical-align:top;text-align:center;">
            <p style="margin:0 0 4px 0;font-size:18px;">📦</p>
            <p style="margin:0;font-size:12px;color:#6B7280;line-height:1.5;">Bezorging binnen<br>4–8 werkdagen</p>
        </td>
        <td style="padding:0 4px;width:33%;vertical-align:top;text-align:center;">
            <p style="margin:0 0 4px 0;font-size:18px;">🔥</p>
            <p style="margin:0;font-size:12px;color:#6B7280;line-height:1.5;">Specialist in kachels<br>en kachelvloeistof</p>
        </td>
        <td style="padding:0 0 0 8px;width:33%;vertical-align:top;text-align:center;">
            <p style="margin:0 0 4px 0;font-size:18px;">✉️</p>
            <p style="margin:0;font-size:12px;color:#6B7280;line-height:1.5;">Vragen? Mail ons<br>gerust</p>
        </td>
    </tr>
</table>

<p style="margin:28px 0 0 0;font-size:14px;color:#6B7280;line-height:1.7;">
    Met vriendelijke groet,<br>
    <strong style="color:#10263D;">Kachelvloeistof.nl</strong>
</p>

@endsection
