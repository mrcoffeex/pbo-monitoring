<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static string $layout = 'filament-panels::components.layout.base';

    /**
     * @var view-string
     */
    protected static string $view = 'filament.pages.auth.login';

    public function getHeading(): string|Htmlable
    {
        return 'Sign in to the dashboard';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Use your office account. Access follows your role.';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Sign in';
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtraBodyAttributes(): array
    {
        return [
            ...parent::getExtraBodyAttributes(),
            'class' => 'login-page',
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Sign in')
            ->extraAttributes(['class' => 'login-submit']);
    }
}
