<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\EntityType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class SupportController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with('roles')->paginate(20);

        return view('support.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::all();
        return view('support.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        if ($request->role === 'individual') {
            $entityType = EntityType::first();

            Customers::create([
                'id_usuario' => $user->id,
                'nombre_contacto' => $user->name,
                'id_entidad' => $entityType ? $entityType->id : 1,
            ]);
        }

        return redirect()->route('support.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['customer.cars', 'customer.rentals', 'roles']);

        return view('support.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        if (auth()->id() === $user->id) {
            return redirect()->route('support.users.index')->with('error', 'No puedes editar tu propio usuario desde aquí. Usa tu perfil.');
        }

        $roles = Role::all();
        $user->load(['customer.individual', 'customer.dealership']);

        return view('support.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        if (auth()->id() === $user->id) {
            return redirect()->route('support.users.index')->with('error', 'No puedes editar tu propio usuario desde aquí.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'nombre_contacto' => ['nullable', 'string', 'max:255'],
            'dni' => ['nullable', 'string', 'max:20'],
            'nif' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles([$request->role]);

        if ($user->customer) {
            $user->customer->update([
                'telefono' => $request->telefono,
                'nombre_contacto' => $request->nombre_contacto ?? $request->name,
            ]);

            if ($user->customer->individual && $request->filled('dni')) {
                $user->customer->individual->update(['dni' => $request->dni]);
            }

            if ($user->customer->dealership && $request->filled('nif')) {
                $user->customer->dealership->update(['nif' => $request->nif]);
            }
        }

        return redirect()->route('support.users.show', $user)->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes borrar tu propia cuenta desde aquí.');
        }

        $user->delete();

        return redirect()->route('support.users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
