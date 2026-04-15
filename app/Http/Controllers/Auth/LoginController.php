<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is active
            if ($user->status !== 'active') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => __('Your account has been deactivated. Please contact your administrator.'),
                ]);
            }

            // Ensure user has tenant
            if (!$user->tenant_id) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => __('Your account is not associated with a tenant. Please contact your administrator.'),
                ]);
            }

            $request->session()->regenerate();
            
            // Log login activity
            $this->activityLogService->logLogin($user, $request);
            
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout activity before logging out
        if ($user) {
            $this->activityLogService->logLogout($user, $request);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
