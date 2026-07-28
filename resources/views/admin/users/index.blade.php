@extends('admin.layouts.app')

@section('title', 'Gebruikers')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Accountbeheer</div>
            <h1 class="text-2xl font-bold text-gray-900">Gebruikers</h1>
            <p class="mt-1 text-sm text-gray-500">Bekijk klanten, beheer accounts en bepaal wie toegang heeft tot het beheerpaneel.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['label' => 'Totaal gebruikers', 'value' => $stats['total'], 'color' => 'text-gray-900'],
            ['label' => 'Klanten', 'value' => $stats['customers'], 'color' => 'text-blue-700'],
            ['label' => 'Beheerders', 'value' => $stats['admins'], 'color' => 'text-emerald-700'],
            ['label' => 'Nieuw deze maand', 'value' => $stats['new_this_month'], 'color' => 'text-violet-700'],
        ] as $stat)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $stat['label'] }}</p>
                <p class="mt-2 text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value'], 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 border-b bg-gray-50/70 p-4 lg:flex-row lg:items-center">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>
                <input type="search" name="q" value="{{ $search }}" placeholder="Zoek op naam, e-mail of telefoon…" class="w-full rounded-xl border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <select name="role" class="rounded-xl border-gray-300 bg-white py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="all" @selected($role === 'all')>Alle rollen</option>
                <option value="customer" @selected($role === 'customer')>Alleen klanten</option>
                <option value="admin" @selected($role === 'admin')>Alleen beheerders</option>
            </select>
            <button class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Filteren</button>
            @if($search !== '' || $role !== 'all')
                <a href="{{ route('admin.users.index') }}" class="px-2 py-2.5 text-center text-sm font-semibold text-gray-500 hover:text-gray-900">Wissen</a>
            @endif
        </form>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left text-sm">
                <thead class="bg-white text-xs uppercase tracking-wide text-gray-400">
                    <tr class="border-b">
                        <th class="px-5 py-3 font-semibold">Gebruiker</th>
                        <th class="px-5 py-3 font-semibold">Contact</th>
                        <th class="px-5 py-3 font-semibold">Bestellingen</th>
                        <th class="px-5 py-3 font-semibold">Rol</th>
                        <th class="px-5 py-3 font-semibold">Aangemaakt</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="group hover:bg-gray-50/80">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-sm font-bold text-blue-700">{{ mb_strtoupper(mb_substr($user->name ?: $user->email, 0, 1)) }}</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400">#{{ $user->id }} @if(auth()->id() === $user->id) · Jij @endif</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <a href="mailto:{{ $user->email }}" class="block text-gray-700 hover:text-blue-600">{{ $user->email }}</a>
                                <span class="text-xs text-gray-400">{{ $user->phone ?: 'Geen telefoonnummer' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-semibold text-gray-800">{{ $user->orders_count }}</span>
                                <span class="text-xs text-gray-400">bestellingen</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_admin ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $user->is_admin ? 'Beheerder' : 'Klant' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500">
                                {{ $user->created_at?->format('d-m-Y') }}
                                <span class="block text-xs text-gray-400">{{ $user->created_at?->diffForHumans() }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">Bewerken</a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Verwijderen</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <p class="font-semibold text-gray-700">Geen gebruikers gevonden</p>
                                <p class="mt-1 text-sm text-gray-400">Pas de zoekopdracht of het filter aan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-gray-100 md:hidden">
            @forelse($users as $user)
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">{{ mb_strtoupper(mb_substr($user->name ?: $user->email, 0, 1)) }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-2 py-1 text-[11px] font-semibold {{ $user->is_admin ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $user->is_admin ? 'Beheerder' : 'Klant' }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-xs text-gray-400">
                                <span>{{ $user->orders_count }} bestellingen</span>
                                <span>Sinds {{ $user->created_at?->format('d-m-Y') }}</span>
                            </div>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="flex-1 rounded-lg bg-gray-900 px-3 py-2 text-center text-xs font-semibold text-white">Bewerken</a>
                                @if(auth()->id() !== $user->id)
                                    <form class="flex-1" action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="w-full rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600">Verwijderen</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-sm text-gray-500">Geen gebruikers gevonden.</div>
            @endforelse
        </div>

        @if($users->hasPages())
            <div class="border-t bg-gray-50 px-4 py-3">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
