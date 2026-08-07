<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
    </style>
</head>
<body>

<h2>Factuur #{{ $order->id }}</h2>

<p>
    <strong>Kachelvloeistof.nl</strong><br>
    info@kachelvloeistof.nl<br>
    KvK: 77355431<br>
    Btw-id: NL003184350B48
</p>

<hr>

<p>
    <strong>Klant:</strong><br>
    {{ $order->name }}<br>
    {{ $order->address }}<br>
    {{ $order->postcode }} {{ $order->city }}
</p>

@if($promotionName = $order->items->pluck('promotion_name')->filter()->first())
<p><strong>Actiebundel:</strong> {{ $promotionName }}</p>
@endif

<table width="100%" cellpadding="6" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>Product</th>
            <th>Aantal</th>
            <th>Prijs</th>
            <th>Subtotaal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td align="center">{{ $item->quantity }}</td>
                <td align="right">€ {{ number_format($item->price, 2, ',', '.') }}</td>
                <td align="right">€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top: 20px;">
    <strong>Totaal:</strong> € {{ number_format($order->total, 2, ',', '.') }}
</p>

</body>
</html>
