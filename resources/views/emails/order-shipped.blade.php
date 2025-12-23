<h2>Goed nieuws {{ $order->name }}!</h2>

<p>
    Je bestelling <strong>#{{ $order->id }}</strong> wordt morgen geleverd 🎉
</p>

<p>
    Leveradres:<br>
    {{ $order->address }}<br>
    {{ $order->postcode }} {{ $order->city }}
</p>

<p>
    Heb je vragen? Neem gerust contact met ons op.
</p>

<p>
    Met vriendelijke groet,<br>
    <strong>Oliehandel van Deutekom</strong>
</p>
