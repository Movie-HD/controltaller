<?php

namespace App\Filament\Resources\Vehiculos\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Vehiculos\VehiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListVehiculos extends ListRecords
{
    protected static string $resource = VehiculoResource::class;

    #public function getTitle(): string
    #{

        #return "Hola";
    #}

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
