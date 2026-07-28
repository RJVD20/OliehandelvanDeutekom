<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $role = $request->query('role', 'all');

        $users = User::query()
            ->withCount('orders')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($role === 'admin', fn ($query) => $query->where('is_admin', true))
            ->when($role === 'customer', fn ($query) => $query->where('is_admin', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('is_admin', true)->count(),
            'customers' => User::where('is_admin', false)->count(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'search', 'role'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'phone'    => ['nullable','string','max:30','regex:/^(?:\\+31|0)[1-9][0-9][\\s-]?\\d{6,7}$/'],
            'is_admin' => 'nullable|boolean',
        ]);

        // Normalize phone
        if (!empty($data['phone'])) {
            $data['phone'] = preg_replace('/[\s-]+/', '', $data['phone']);
        }

        // Ensure boolean value
        $data['is_admin'] = !empty($data['is_admin']);

        if ($user->is(auth()->user()) && ! $data['is_admin']) {
            return back()->withInput()->with('error', 'Je kunt je eigen adminrechten niet intrekken.');
        }

        if ($user->is_admin && ! $data['is_admin'] && User::where('is_admin', true)->count() <= 1) {
            return back()->withInput()->with('error', 'Er moet minimaal één beheerder overblijven.');
        }

        $before = $user->only(['name', 'email', 'phone', 'is_admin']);
        $user->update($data);
        AuditLog::record('updated', 'user', $user->id, $user->name, $before, $user->only(['name', 'email', 'phone', 'is_admin']));

        return redirect()->route('admin.users.index')->with('toast', 'Gebruiker bijgewerkt');
    }

    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'Je kunt je eigen account niet verwijderen.');
        }

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->with('error', 'De laatste beheerder kan niet worden verwijderd.');
        }

        AuditLog::record('deleted', 'user', $user->id, $user->name, $user->only(['name', 'email', 'phone', 'is_admin']));
        $user->delete();
        return redirect()->route('admin.users.index')->with('toast', 'Gebruiker verwijderd');
    }

    public function toggleAdmin(Request $request, User $user)
    {
        $makeAdmin = $request->boolean('is_admin');

        if ($user->is(auth()->user()) && ! $makeAdmin) {
            return back()->with('error', 'Je kunt je eigen adminrechten niet intrekken.');
        }

        if ($user->is_admin && ! $makeAdmin && User::where('is_admin', true)->count() <= 1) {
            return back()->with('error', 'Er moet minimaal één beheerder overblijven.');
        }

        $before = ['is_admin' => $user->is_admin];
        $user->is_admin = $makeAdmin;
        $user->save();
        AuditLog::record('updated', 'user', $user->id, $user->name, $before, ['is_admin' => $user->is_admin]);

        if ($request->wantsJson()) {
            return response()->json(['is_admin' => $user->is_admin]);
        }

        return redirect()->route('admin.users.index')->with('toast', 'Gebruiker bijgewerkt');
    }
}
