@extends('admin.layouts.app')

@section('title', 'Gebruikers')

@section('content')
<h1 class="text-2xl font-bold mb-6">Gebruikers</h1>

<div class="hidden md:block bg-white p-4 rounded shadow overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b">
                <th class="p-2">#</th>
                <th class="p-2">Naam</th>
                <th class="p-2">E-mail</th>
                <th class="p-2">Telefoon</th>
                <th class="p-2">Admin</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2">{{ $user->id }}</td>
                    <td class="p-2">
                        {{ $user->name }}
                        @if(config('app.debug'))
                            <div class="text-xs text-gray-400">DB: {{ $user->getRawOriginal('name') }}</div>
                        @endif
                    </td>
                    <td class="p-2">{{ $user->email }}</td>
                    <td class="p-2">{{ $user->phone ?? '-' }}</td>
                    <td class="p-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->is_admin ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $user->is_admin ? 'Ja' : 'Nee' }}
                        </span>
                    </td>
                    <td class="p-2 text-right">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 mr-3">Bewerk</a>

                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Verwijder gebruiker?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Verwijder</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

<div class="md:hidden space-y-3">
    @foreach($users as $user)
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold leading-tight">{{ $user->name }}</p>
                    @if(config('app.debug'))
                        <p class="text-xs text-gray-400">DB: {{ $user->getRawOriginal('name') }}</p>
                    @endif
                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                    <p class="text-xs text-gray-500">{{ $user->phone ?? '-' }}</p>
                </div>
                <div class="flex flex-col items-end text-sm text-gray-700">
                    <span class="text-xs uppercase tracking-wide text-gray-500">Admin</span>
                    <span class="mt-1 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $user->is_admin ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $user->is_admin ? 'Ja' : 'Nee' }}
                    </span>
                </div>
            </div>

            <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex justify-center rounded-lg bg-blue-600 px-4 py-2 text-white text-sm font-semibold">Bewerk</a>

                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Verwijder gebruiker?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex justify-center rounded-lg bg-red-600 px-4 py-2 text-white text-sm font-semibold">Verwijder</button>
                </form>
            </div>
        </div>
    @endforeach

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

@endsection
