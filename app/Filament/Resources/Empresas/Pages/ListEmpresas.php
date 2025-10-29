<?php

namespace App\Filament\Resources\Empresas\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Empresas\EmpresaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmpresas extends ListRecords
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
