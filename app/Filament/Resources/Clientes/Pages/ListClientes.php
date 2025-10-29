<?php

namespace App\Filament\Resources\Clientes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Clientes\ClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
