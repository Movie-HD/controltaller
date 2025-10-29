<?php

namespace App\Filament\Resources\Mecanicos\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Mecanicos\MecanicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMecanico extends EditRecord
{
    protected static string $resource = MecanicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
