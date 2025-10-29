<?php

namespace App\Filament\Pages\Auth;

class Login extends \Filament\Auth\Pages\Login
{
    public function mount(): void
    {
        parent::mount();

        if (app()->environment(['local', 'testing'])) {
            $this->form->fill([
                'email' => 'test@example.com',
                'password' => 'password',
                'remember' => true,
            ]);
        }
    }
}
