<?php

namespace App\Filament\Resources\VehiculoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput; # Agregar si es un Input [Form]
use Filament\Forms\Components\Select; # Agregar si es un Select [Form]
use Filament\Forms\Components\Textarea; # Agregar si es un Textarea [Form]
use Filament\Tables\Columns\TextColumn; # Agregar si es un Column [Table]
use Filament\Forms\Components\Grid;

class ReparacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'reparaciones';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make([
                'default' => 2, // Por defecto, usa 1 columna para pantallas pequeñas.
                'sm' => 3, // A partir del tamaño 'sm', usa 2 columnas.
            ])
            ->schema([
                # Campo Descripción
                TextArea::make('descripcion')
                    ->label('Descripcion de Reparación') 
                    ->required()
                    ->columnSpan([
                        'default' => 2, // Por defecto, ocupa 1 columna en dispositivos pequeños.
                        'sm' => 3, // Ocupa 2 columnas en dispositivos grandes.
                    ]),

                # Campo Servicios
                TextInput::make('servicios')
                    ->label('Repuestos Cambiados')
                    ->required(),

                # Campo Kilometraje
                TextInput::make('kilometraje')
                    ->label('Kilometraje')
                    ->numeric()
                    ->required(),

                # Campo Notas
                TextInput::make('notas')
                    ->label('Notas Adicionales')
                    ->nullable(),

                # Campo Precio
                TextInput::make('precio')
                    ->label('Precio')
                    ->nullable(),
                
                # Campo Mecánico
                Select::make('mecanico_id')
                    ->label('Mecánico')
                    ->relationship('mecanico', 'nombre') # Nombre del metodo de la relacion + campo a mostrar
                    ->options(function (callable $get) {
                        // Filtra los mecánicos según la sucursal seleccionada
                        return \App\Models\Mecanico::where('sucursal_id', $get('sucursal_id'))->pluck('nombre', 'id');
                    })
                    ->required()
                    ->searchable()
                    # SubModal para crear un nuevo Mecánico
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required(),
                        Select::make('empresa_id')
                            ->label('')
                            ->relationship('empresa', 'nombre')
                            ->preload()
                            ->searchable()
                            ->default(1)
                            ->extraAttributes(['style' => 'display:none;']),
                        Select::make('sucursal_id')
                            ->label('')
                            ->relationship('sucursal', 'nombre')
                            ->preload()
                            ->searchable()
                            ->default(1)
                            ->extraAttributes(['style' => 'display:none;']),
                    ]),

                # Campo Cliente
                Select::make('cliente_id')
                    ->label('')
                    ->relationship('cliente', 'nombre') # Nombre del metodo de la relacion + campo a mostrar
                    ->required()
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->cliente_id) // Usa el cliente del vehículo relacionado
                    ->extraAttributes(['style' => 'display:none;']),

                # Campo Vehículo
                Select::make('vehiculo_id')
                    ->label('')
                    ->relationship('vehiculo', 'placa') # Nombre del metodo de la relacion + campo a mostrar
                    ->required()
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id) // Usa el ID del vehículo relacionado
                    ->extraAttributes(['style' => 'display:none;']),

                # Campo Empresa
                Select::make('empresa_id')
                    ->label('')
                    ->relationship('empresa', 'nombre') # Nombre del metodo de la relacion + campo a mostrar
                    ->required()
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->cliente->empresa_id) // Usa la empresa del cliente relacionado
                    ->extraAttributes(['style' => 'display:none;']),

                # Campo Sucursal
                Select::make('sucursal_id')
                    ->label('')
                    ->relationship('sucursal', 'nombre') # Nombre del metodo de la relacion + campo a mostrar
                    ->default(fn (RelationManager $livewire) => $livewire->getSucursalId()) // Lógica para obtener la sucursal asignada
                    ->required()
                    ->reactive() // Permite actualizar dinámicamente el campo de mecánicos
                    ->options(function (RelationManager $livewire) {
                        // Si el usuario no tiene una sucursal asignada, permite seleccionar manualmente.
                        return $livewire->getSucursalOptions();
                    })
                    ->extraAttributes(fn (RelationManager $livewire) => [
                        'style' => $livewire->getSucursalId() !== null ? 'display:none;' : '',
                    ]), // Oculta el campo si ya hay una sucursal asignada.
                    
            ])
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                # Campo Descripción
                TextColumn::make('descripcion')
                    ->label('Descripcion de Reparación'),
                
                # Campo Servicios
                TextColumn::make('servicios')
                    ->label('Repuestos Cambiados'),

                # Campo Notas
                TextColumn::make('notas'),

                # Campo Precio
                TextColumn::make('precio'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getSucursalId(): ?int
{
    $user = auth()->user();

    // Si el usuario tiene una sucursal asignada, usar esa.
    if ($user->sucursal_id) {
        return $user->sucursal_id;
    }

    // Si es administrador y no tiene sucursal, retorna null (o una lógica predeterminada).
    return null;
}

public function getSucursalOptions(): array
{
    $user = auth()->user();

    // Si el usuario tiene una sucursal asignada, limitar a esa.
    if ($user->sucursal_id) {
        return \App\Models\Sucursal::where('id', $user->sucursal_id)->pluck('nombre', 'id')->toArray();
    }

    // Si es administrador, mostrar todas las sucursales.
    return \App\Models\Sucursal::pluck('nombre', 'id')->toArray();
}

}
