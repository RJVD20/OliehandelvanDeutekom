@extends('emails.layout')

@section('title', 'Google Routes-limiet bereikt')

@section('content')
<h1 style="margin:0 0 12px;font-size:22px;color:#10263D;">Google Routes-limiet bereikt</h1>

<p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#4B5563;">
    In {{ $period }} zijn {{ $used }} van de ingestelde {{ $limit }} Google Routes-aanvragen gebruikt.
</p>

<p style="margin:0;font-size:15px;line-height:1.7;color:#4B5563;">
    Nieuwe routevoorstellen gebruiken tot het begin van de volgende maand automatisch de Mapbox-fallback.
</p>
@endsection
