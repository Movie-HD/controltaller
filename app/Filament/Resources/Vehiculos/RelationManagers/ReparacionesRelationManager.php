<?php

namespace App\Filament\Resources\Vehiculos\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput; # Agregar si es un Input [Form]
use Filament\Forms\Components\Select; # Agregar si es un Select [Form]
use Filament\Forms\Components\Textarea; # Agregar si es un Textarea [Form]
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Hidden; # Agregar si es un Hidden [Form]
use Filament\Forms\Get; # Agregar para funcion de opciones
use Illuminate\Support\Collection; # Agregar para funcion de opciones
use Filament\Forms\Set; # Agregar para afterStateUpdated [Form]

class ReparacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'reparaciones';

    public function form(Schema $schema): Schema
    {
        return $schema
        ->components([
            Grid::make([
                'default' => 2, // Por defecto, usa 1 columna para pantallas pequeñas.
                'sm' => 2, // A partir del tamaño 'sm', usa 2 columnas.
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
                TextArea::make('servicios')
                    ->label('Repuestos Cambiados')
                    ->required()
                    ->columnSpan([
                        'default' => 2, // Por defecto, ocupa 1 columna en dispositivos pequeños.
                        'sm' => 3, // Ocupa 2 columnas en dispositivos grandes.
                    ]),

                # Campo Notas
                TextArea::make('notas')
                    ->label('Notas Adicionales')
                    ->nullable()
                    ->columnSpan([
                        'default' => 2, // Por defecto, ocupa 1 columna en dispositivos pequeños.
                        'sm' => 3, // Ocupa 2 columnas en dispositivos grandes.
                    ]),

                # Campo Kilometraje
                TextInput::make('kilometraje')
                    ->label('Kilometraje')
                    ->numeric()
                    ->required()
                    ->suffix('km'),

                # Campo Precio
                TextInput::make('precio')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('S/.')
                    ->nullable(),

                # Campo Cliente
                Hidden::make('cliente_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->cliente_id), // Usa el cliente del vehículo relacionado

                # Campo Vehículo
                Hidden::make('vehiculo_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id), // Usa el ID del vehículo relacionado

                # Campo Empresa
                Hidden::make('empresa_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->cliente->empresa_id), // Usa la empresa del cliente relacionado

                # Campo Mecánico
                Select::make('mecanico_id')
                        ->label('Mecánico')
                        ->relationship('mecanico', 'nombre')
                        ->required()
                        ->live()
                        ->searchable()
                        ->preload()

                        # SubModal para crear un nuevo Mecánico
                        ->createOptionForm([
                            TextInput::make('nombre')
                                ->label('Nombre')
                                ->required(),
                            Select::make('empresa_id')
                                ->label('')
                                ->relationship('empresa', 'nombre')
                                ->default(1)
                                ->extraAttributes(['style' => 'display:none;']),
                        ])
                        ->columnSpan([
                            'default' => 2, // Por defecto, ocupa 1 columna en dispositivos pequeños.
                            'sm' => 2, // Ocupa 2 columnas en dispositivos grandes.
                        ]),

                    ])
                ]);
            }

            public function table(Table $table): Table
            {
        return $table
            ->defaultSort('created_at', 'desc') # Ordenar por fecha de creación
            ->recordTitleAttribute('descripcion')
            ->columns([
                # Campo Descripción
                TextColumn::make('descripcion')
                    ->label('Descripcion de Reparación'),

                # Campo Precio
                TextColumn::make('precio')
                    ->prefix('S/. '),

                # Campo Servicios
                TextColumn::make('servicios')
                    ->label('Repuestos Cambiados'),

                # Campo Notas
                TextColumn::make('notas')
                    ->extraAttributes(['class' => 'truncate max-w-xs']),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->slideOver(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalHeading('Ver Reparación')
                        ->slideOver(),
                    EditAction::make()
                        ->modalHeading('Editar Reparación')
                        ->slideOver()
                        ->color('primary'),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

}
