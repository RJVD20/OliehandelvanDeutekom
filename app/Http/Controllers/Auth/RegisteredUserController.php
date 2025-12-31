<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'provinces' => nl_provinces(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'    => ['required', 'string', 'max:30', 'regex:/^(?:\\+31|0)[1-9][0-9][\\s-]?\\d{6,7}$/'],
            'address'  => 'nullable|string|max:255',
            'postcode' => ['nullable', 'regex:/^[1-9][0-9]{3}\s?[A-Z]{2}$/i'],
            'city'     => 'nullable|string|max:255',
            'province' => ['required', 'in:' . implode(',', nl_provinces())],
        ]);

        // normalize phone: remove spaces and dashes before storing
        $phone = preg_replace('/[\s-]+/', '', $request->phone);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $phone,
            'password' => Hash::make($request->password),
            'address'  => $request->address,
            'postcode' => $request->postcode,
            'city'     => $request->city,
            'province' => $request->province,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
