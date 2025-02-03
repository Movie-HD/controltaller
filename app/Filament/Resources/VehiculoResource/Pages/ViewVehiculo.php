<?php

namespace App\Filament\Resources\VehiculoResource\Pages;

use App\Filament\Resources\VehiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVehiculo extends ViewRecord
{
    protected static string $resource = VehiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
            ->url($this->getResource()::getUrl('edit', ['record' => $this->record->id]) . '?activeRelationManager=0'),
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
