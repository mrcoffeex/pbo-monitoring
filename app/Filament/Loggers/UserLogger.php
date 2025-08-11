<?php

namespace App\Filament\Loggers;

use App\Models\User;
use App\Filament\Resources\UserResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class UserLogger extends Logger
{
    public static ?string $model = User::class;

    public static function getLabel(): string | Htmlable | null
    {
        return UserResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name')
                    ->label(__('Name')),

                Field::make('email')
                    ->label(__('Email')),

                Field::make('role.name')
                    ->label(__('Role')),
            ])
            ->relationManagers([
                //
            ]);
    }
}
