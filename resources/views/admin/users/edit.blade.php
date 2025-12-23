@extends('admin.layouts.app')

@section('title', 'Bewerk gebruiker')

@section('content')
<h1 class="text-2xl font-bold mb-6">Bewerk gebruiker</h1>

<div class="bg-white p-6 rounded shadow max-w-lg">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label class="block text-sm text-gray-600">Naam</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full mt-1 p-2 border rounded" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm text-gray-600">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full mt-1 p-2 border rounded" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm text-gray-600">Telefoon</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full mt-1 p-2 border rounded">
        </div>

        <div class="mb-4 flex items-center gap-2">
            <input type="checkbox" id="is_admin" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
            <label for="is_admin" class="text-sm text-gray-700">Is admin</label>
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Opslaan</button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 rounded">Annuleren</a>
        </div>
    </form>
</div>

@endsection
