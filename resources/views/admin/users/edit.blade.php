@extends('admin.layouts.app')

@section('title', 'Bewerk gebruiker')

@section('content')
<h1 class="text-2xl font-bold mb-6">Bewerk gebruiker</h1>

<div class="max-w-lg rounded-lg bg-white p-6 shadow">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
        @csrf
        @method('PATCH')

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Naam</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base" required>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base" required>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Telefoon</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
        </div>

        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-sm font-medium text-gray-800">
            <input type="checkbox" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} class="h-5 w-5 rounded border-gray-300">
            <span>Is admin</span>
        </label>

        <div class="flex flex-col sm:flex-row gap-3">
            <button class="w-full sm:w-auto px-5 py-3 bg-blue-600 text-white rounded-lg font-semibold">Opslaan</button>
            <a href="{{ route('admin.users.index') }}" class="w-full sm:w-auto px-5 py-3 rounded-lg border text-center font-semibold text-gray-800">Annuleren</a>
        </div>
    </form>
</div>

@endsection
