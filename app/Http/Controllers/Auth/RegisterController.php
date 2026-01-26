<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->tenant_name,
            'email' => $request->email,
            'status' => 'active',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id,
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Seed roles and permissions for the new tenant
        $seeder = new RolePermissionSeeder();
        $seeder->seedRolesAndPermissions($tenant);

        // Sync user with admin role
        $user->syncRoleModel();

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
