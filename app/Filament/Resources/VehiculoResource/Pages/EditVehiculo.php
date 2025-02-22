<?php

namespace App\Filament\Resources\VehiculoResource\Pages;

use App\Filament\Resources\VehiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehiculo extends EditRecord
{
    protected static string $resource = VehiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
    public function getContentTabLabel(): ?string
    {
        return 'Datos del Vehículo';
    }
}
