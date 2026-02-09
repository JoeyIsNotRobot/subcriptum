<?php

namespace App\Filament\Admin\Pages;

use Filament\Auth\Pages\Login;

class AdminLogin extends Login
{
    public function mount(): void
    {
        parent::mount();

        if(app()->isLocal() == 'localhost'){
            $this->form->fill([
                'email' => 'hectorcoelho@hotmail.com',
                'password' => '123mudar',
                'remember' => true
            ]);
        }
    }
}
