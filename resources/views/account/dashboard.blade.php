@extends('themes.default.layouts.app')

@section('title', 'Account')

@section('content')
    <div class="text-center py-20">
        <p class="text-lg">Deze pagina is verwijderd. Je wordt doorgestuurd naar je profiel...</p>
    </div>

    <script>
        window.location = "{{ route('profile.edit') }}";
    </script>
@endsection
