<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'client', 'managedLocations']);

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();
        $clients = Client::where('is_active', true)->get();

        return view('users.index', compact('users', 'roles', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'client_id' => $request->client_id,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        $user->assignRole($request->role);

        AuditLog::log('user_created', "Created user {$user->name} ({$user->email}) with role {$request->role}.", $user);

        return redirect()->route('users.index')->with('status', "User '{$user->name}' created successfully.");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $oldRole = $user->getRoleNames()->first();
        $user->syncRoles([$request->role]);
        $user->update(['status' => $request->status]);

        AuditLog::log('user_updated', "Updated user {$user->name}: Role from {$oldRole} to {$request->role}, status: {$request->status}.", $user);

        return back()->with('status', "User '{$user->name}' updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        AuditLog::log('user_deleted', "Deleted user {$userName}.", $user);

        return back()->with('status', "User '{$userName}' removed.");
    }
}
