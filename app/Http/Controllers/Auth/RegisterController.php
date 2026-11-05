<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole('Super Admin')) {
                return redirect('/platform/dashboard');
            }

            if ($user->hasRole('School Admin') || $user->hasRole('Principal')) {
                return redirect('/school/dashboard');
            }

            return redirect('/dashboard');
        }

        return view('livewire.auth.register');
    }
}
