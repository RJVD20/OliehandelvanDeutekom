<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => ['nullable','string','max:30','regex:/^(?:\\+31|0)[1-9][0-9][\\s-]?\\d{6,7}$/'],
            'is_admin' => 'nullable|boolean',
        ]);

        // Normalize phone
        if (!empty($data['phone'])) {
            $data['phone'] = preg_replace('/[\s-]+/', '', $data['phone']);
        }

        // Ensure boolean value
        $data['is_admin'] = !empty($data['is_admin']);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('toast', 'Gebruiker bijgewerkt');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('toast', 'Gebruiker verwijderd');
    }

    public function toggleAdmin(Request $request, User $user)
    {
        $user->is_admin = (bool) $request->input('is_admin');
        $user->save();

        if ($request->wantsJson()) {
            return response()->json(['is_admin' => $user->is_admin]);
        }

        return redirect()->route('admin.users.index')->with('toast', 'Gebruiker bijgewerkt');
    }
}
