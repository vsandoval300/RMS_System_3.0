<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('microsoft')
            ->scopes([
                'openid',
                'profile',
                'email',
            ])
            ->with([
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    public function callback()
    {
        $microsoftUser = Socialite::driver('microsoft')->user();

        $entraObjectId = $microsoftUser->getId();
        $entraTenantId = config('services.microsoft.tenant');

        $email = $microsoftUser->getEmail();
        $name = $microsoftUser->getName();

        if (! $entraObjectId || ! $entraTenantId || ! $email) {
            return $this->redirectToLogin(
                'Unable to verify your Microsoft account.'
            );
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->first();

        if (! $user) {
            return $this->redirectToLogin(
                'Your Microsoft account is not registered in RMS.'
            );
        }

        if (
            empty($user->entra_tenant_id) &&
            empty($user->entra_object_id)
        ) {
            $user->update([
                'entra_tenant_id' => $entraTenantId,
                'entra_object_id' => $entraObjectId,
            ]);
        } elseif (
            $user->entra_tenant_id !== $entraTenantId ||
            $user->entra_object_id !== $entraObjectId
        ) {
            return $this->redirectToLogin(
                'Your Microsoft account is not authorized for this RMS user.'
            );
        }

        $user->update([
            'name' => $name ?: $user->name,
            'email' => $email,
        ]);

        Auth::login($user, remember: true);

        request()->session()->regenerate();

        LoginLog::create([
            'user_id' => $user->id,
            'logged_in_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->intended('/admin');
    }

    protected function redirectToLogin(string $message)
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->route('filament.admin.auth.login')
            ->with('error', $message);
    }
}