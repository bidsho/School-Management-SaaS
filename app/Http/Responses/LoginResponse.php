<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('Super Admin')) {
            $destination = '/platform/dashboard';

        } elseif ($user->hasRole('Treasury')) {
            $destination = '/treasury/dashboard';

        } elseif (
            $user->hasRole('School Admin') ||
            $user->hasRole('Principal')
        ) {
            $destination = '/school/dashboard';

        } elseif ($user->hasRole('Teacher')) {
            $destination = '/teacher/dashboard';

        } elseif ($user->hasRole('Accountant')) {
            $destination = '/accountant/dashboard';

        } elseif ($user->hasRole('Receptionist')) {
            $destination = '/receptionist/dashboard';

        } elseif ($user->hasRole('Parent')) {
            $destination = '/parent/dashboard';

        } elseif ($user->hasRole('Student')) {
            $destination = '/student/dashboard';

        } else {
            $destination = '/dashboard';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'two_factor' => false,
                'redirect' => $destination,
            ]);
        }

        return redirect()->to($destination);
    }
}
