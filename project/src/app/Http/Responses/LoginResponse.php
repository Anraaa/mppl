<?php

namespace App\Http\Responses;

use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;
 
class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }

        if ($user->hasRole('user')) {
            return redirect()->to(Dashboard::getUrl(panel: 'client'));
        }

        return parent::toResponse($request);
    }
}
