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
use Filament\Forms\Components\Hidden; # Agregar si es un Hidden [Form]
use Filament\Forms\Get; # Agregar para funcion de opciones
use Illuminate\Support\Collection; # Agregar para funcion de opciones
use Filament\Forms\Set; # Agregar para afterStateUpdated [Form]

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
                
                # Campo Cliente
                Hidden::make('cliente_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->cliente_id), // Usa el cliente del vehículo relacionado
                    
                # Campo Vehículo
                Hidden::make('vehiculo_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id), // Usa el ID del vehículo relacionado
                    
                # Campo Empresa
                Hidden::make('empresa_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->cliente->empresa_id), // Usa la empresa del cliente relacionado
                    
                # Campo Sucursal
                Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->options(function (Get $get): Collection {
                        $user = auth()->user();
                        
                        // Si el usuario tiene el rol de admin
                        if ($user->hasRole('admin')) {
                            // Retorna todas las sucursales
                            return \App\Models\Sucursal::query()
                                ->pluck('nombre', 'id');
                        }
                        
                        // Para otros usuarios, retorna solo su sucursal asignada
                        return \App\Models\Sucursal::query()
                            ->where('id', $user->sucursal_id) // Suponiendo que el usuario tiene un campo sucursal_id
                            ->pluck('nombre', 'id');
                    })
                    ->default(function (Get $get) {
                        $user = auth()->user();
                        $sucursalesCount = \App\Models\Sucursal::query()->count();
                        $singleSucursal = \App\Models\Sucursal::query()->first();
                
                        // Si el usuario es admin y no tiene sucursal definida
                        if ($user->hasRole('admin') && !$user->sucursal_id) {
                            // Si solo existe una sucursal, usarla por defecto
                            if ($sucursalesCount === 1) {
                                return $singleSucursal->id;
                            }
                
                            // Si hay varias sucursales, no establecer un valor por defecto
                            return null;
                        }
                
                        // Si es admin con sucursal asignada o cualquier otro usuario, seleccionar su sucursal
                        return $user->sucursal_id;
                    })
                    ->required()
                    ->live()
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(fn (Set $set) => $set('mecanico_id', null)), # Con una COMA al final del set se pueden agregar mas campos para limpiar.

                # Campo Mecánico
                Select::make('mecanico_id')
                        ->label('Mecánico')
                        ->options(fn (Get $get): Collection => \App\Models\Mecanico::query()
                            ->where('sucursal_id', $get('sucursal_id'))
                            ->pluck('nombre', 'id'))
                        ->required()
                        ->live()
                        ->searchable()
                        ->preload()
                        
                        # SubModal para crear un nuevo Mecánico
                        ->createOptionForm([
                            TextInput::make('nombre')
                                ->label('Nombre')
                                ->required(),
                            Hidden::make('empresa_id')
                                ->default(1),
                            Hidden::make('sucursal_id')
                                ->default(1),
                        ])
                        ->columnSpan([
                            'default' => 2, // Por defecto, ocupa 1 columna en dispositivos pequeños.
                            'sm' => 3, // Ocupa 2 columnas en dispositivos grandes.
                        ]),
                    
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
    
}
