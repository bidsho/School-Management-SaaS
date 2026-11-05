<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth()->check()) {
            return $this->redirectByRole();
        }

        return view('livewire.auth.login');
    }

    private function redirectByRole(): RedirectResponse
    {
        $user = auth()->user();

        // Super Admin dashboard
        if ($user->hasRole('Super Admin')) {
            return redirect('/platform/dashboard');
        }

        // School Admin dashboard
        if ($user->hasRole('School Admin')) {
            return redirect('/school/dashboard');
        }

        // Principal dashboard
        if ($user->hasRole('Principal')) {
            return redirect('/school/dashboard');
        }

        // Treasury dashboard
        
        if ($user->hasRole('Treasury')) {
            return redirect('/treasury/dashboard');
        }

        // Accountant dashboard
        if ($user->hasRole('Accountant')) {
            return redirect('/accountant/dashboard');
        }

        // Teacher dashboard
        if ($user->hasRole('Teacher')) {
            return redirect('/teacher/dashboard');
        }

        // Receptionist dashboard
        if ($user->hasRole('Receptionist')) {
            return redirect('/receptionist/dashboard');
        }

        // Parent dashboard
        if ($user->hasRole('Parent')) {
            return redirect('/parent/dashboard');
        }

        // Student dashboard
        if ($user->hasRole('Student')) {
            return redirect('/student/dashboard');
        }

        // Fallback for users without an assigned role
        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login')->withErrors([
            'email' => 'Your account has no assigned role. Please contact the administrator.',
        ]);
    }
}