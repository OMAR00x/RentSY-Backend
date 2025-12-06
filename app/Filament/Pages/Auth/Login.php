<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getPhoneFormComponent(),
            $this->getPasswordFormComponent(),
        ]);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('رقم الهاتف')
            ->required()
            ->autofocus()
            ->autocomplete('tel')
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function throwFailureValidationException(): never
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'data.phone' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'phone' => $data['phone'],
            'password' => $data['password'],
        ];
    }
}
