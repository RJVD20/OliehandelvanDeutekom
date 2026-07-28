@extends('themes.default.layouts.app')

@section('title', $entry->title ?? '')
@section('description', Str::limit(strip_tags($entry->content ?? ''), 155))

@section('content')
<section class="prose prose-green max-w-4xl">
    @if(!empty($entry?->title))
        <h1>{{ $entry->title }}</h1>
    @endif
    {!! nl2br(e($entry->content ?? '')) !!}
</section>
@endsection
