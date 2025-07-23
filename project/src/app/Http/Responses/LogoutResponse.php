<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Filament\Http\Responses\Auth\LogoutResponse as BaseLogoutResponse;

class LogoutResponse extends BaseLogoutResponse
{
    public function toResponse(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return redirect()->to('/client/login');
    }
}
