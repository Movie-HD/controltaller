<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->action(function (array $data) {
                try {
                    // Intentar eliminar el registro
                    $this->record->delete();
                    Notification::make()
                        ->title('Cliente eliminado')
                        ->body('El cliente ha sido eliminado exitosamente.')
                        ->success()
                        ->send();
                } catch (QueryException $e) {
                    // Verificar si es una violación de clave foránea
                    if ($e->getCode() == 23000) {
                        Notification::make()
                            ->title('No se puede eliminar el cliente')
                            ->body('Este cliente tiene vehículos asociados y no se puede eliminar.')
                            ->danger()
                            ->send();
                    } else {
                        // Manejar otros errores
                        Notification::make()
                            ->title('Error')
                            ->body('Ocurrió un error inesperado.')
                            ->danger()
                            ->send();
                    }
                }
            }),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
    public function getContentTabLabel(): ?string
    {
        return 'Datos del Cliente';
    }
}
