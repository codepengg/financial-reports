<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

use Filament\Pages\Auth\Register;

class SingleSignOnController extends Controller
{

    protected $register;

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
            auth()->login($user);
            return redirect()->intended(route('filament.admin.pages.dashboard'));
        } else {

            $this->register->form->fill([
                $provider . '_id' => $response->getId(),
                'name' => $response->getName(),
                'email' => $response->getEmail(),
                'password' => 'admin123',
            ]);

            return $this->register->register();
        }
    }

    protected function validateProvider(string $provider): array
    {
        return $this->getValidationFactory()->make(
            ['provider' => $provider],
            ['provider' => 'in:google']
        )->validate();
    }
}
