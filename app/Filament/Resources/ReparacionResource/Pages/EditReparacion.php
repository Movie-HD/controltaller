<?php

namespace App\Filament\Resources\ReparacionResource\Pages;

use App\Filament\Resources\ReparacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReparacion extends EditRecord
{
    protected static string $resource = ReparacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
