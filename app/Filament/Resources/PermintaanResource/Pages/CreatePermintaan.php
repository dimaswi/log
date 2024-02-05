<?php

namespace App\Filament\Resources\PermintaanResource\Pages;

use App\Filament\Resources\PermintaanResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePermintaan extends CreateRecord
{
    protected static string $resource = PermintaanResource::class;

    protected function getRedirectUrl(): string
    {

        Notification::make()
            ->title('Permintaan Baru')
            ->success()
            ->body('Unit '. auth()->user()->unit. ' membuat permintaan baru!')
            ->sendToDatabase(User::role('Logistik')->get())
            ->broadcast(User::role('Logistik')->get())
            ->toDatabase();

        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
