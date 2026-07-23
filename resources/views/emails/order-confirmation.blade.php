<h2>Bedankt voor je bestelling!</h2>

<p>
    Beste {{ $order->name }},
</p>

<p>
    We hebben je bestelling <strong>#{{ $order->id }}</strong> goed ontvangen.
</p>

<h3>Bestelling</h3>

<ul>
    @foreach($order->items as $item)
        <li>
            {{ $item->quantity }}× {{ $item->product_name }}
            (€ {{ number_format($item->price, 2, ',', '.') }})
        </li>
    @endforeach
</ul>

<p>
    <strong>Totaal:</strong>
    € {{ number_format($order->total, 2, ',', '.') }}
</p>

@if($order->latestPayment?->pay_link)
    <p>
        <a href="{{ $order->latestPayment->pay_link }}">Betaal deze bestelling via Mollie</a>
    </p>
@endif

<h3>Afleveradres</h3>
<p>
    {{ $order->address }}<br>
    {{ $order->postcode }} {{ $order->city }}
</p>

<p>
    Met vriendelijke groet,<br>
    <strong>Oliehandel van Deutekom</strong>
</p>
