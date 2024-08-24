<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Facades\Filament;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

use Filament\Pages\Auth\Register;

class SingleSignOnController extends Controller
{

    protected Register $register;

    public function __construct(Register $register)
    {
        return $this->register = $register;
    }

    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        $response = Socialite::driver($provider)->user();

        $user = User::firstWhere(['email' => $response->getEmail()]);

        if ($user) {
            $user->update([$provider . '_id' => $response->getId()]);

            Filament::auth()->login($user);
            session()->regenerate();

        } else {
            $this->register->form->fill([
                $provider . '_id' => $response->getId(),
                'name' => $response->getName(),
                'email' => $response->getEmail(),
                'password' => 'admin123',
            ]);

            $this->register->register();
        }
        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }

    protected function validateProvider(string $provider): array
    {
        return $this->getValidationFactory()->make(
            ['provider' => $provider],
            ['provider' => 'in:google']
        )->validate();
    }
}
