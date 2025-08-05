<?php

namespace App\Auth\Http\Responses;

use Filament\Facades\Filament;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class UserPanelLogoutResponse implements Responsable
{
    /**
     * @throws \Exception
     */
    public function toResponse($request): RedirectResponse | Redirector
    {
        if (Filament::getCurrentPanel()->getId() === 'admin') {
            return redirect()->to(Filament::getLoginUrl());
        }
        // Redirect to the intended URL without falling back to the dashboard
        return redirect()->back();
    }
}
