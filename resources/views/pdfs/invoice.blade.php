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
    <strong>Oliehandel van Deutekom</strong><br>
    info@oliehandelvandeutekom.nl
</p>

<hr>

<p>
    <strong>Klant:</strong><br>
    {{ $order->name }}<br>
    {{ $order->address }}<br>
    {{ $order->postcode }} {{ $order->city }}
</p>

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
