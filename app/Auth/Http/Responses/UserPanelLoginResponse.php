<?php

namespace App\Auth\Http\Responses;

use Filament\Pages\Dashboard;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class UserPanelLoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }
        // Redirect to the intended URL without falling back to the dashboard
        return redirect()->intended('/');
    }
}
