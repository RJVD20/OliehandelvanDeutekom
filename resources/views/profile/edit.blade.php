@extends('themes.default.layouts.app')

@section('title', 'Profiel')

@section('content')
    <div class="space-y-8">
        <div>
            <p class="text-sm uppercase tracking-wide text-gray-500">Account</p>
            <h1 class="text-3xl font-semibold text-gray-900">Beheer je profiel</h1>
            <p class="text-gray-600 mt-1">Werk je gegevens, wachtwoord en accountinstellingen bij.</p>
        </div>

        <div class="space-y-6">
            <div class="p-6 sm:p-8 bg-white shadow-sm border border-gray-100 rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow-sm border border-gray-100 rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow-sm border border-gray-100 rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
