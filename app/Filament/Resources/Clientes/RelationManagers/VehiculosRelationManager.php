<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
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
use Filament\Tables\Columns\TextColumn; # Agregar si es un Column [Table]

class VehiculosRelationManager extends RelationManager
{
    protected static string $relationship = 'vehiculos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                # Campo Placa
                TextInput::make('placa')
                    ->required()
                    ->label('Placa'),

                # Campo Marca
                TextInput::make('marca')
                    ->required()
                    ->label('Marca'),

                # Campo Modelo
                TextInput::make('modelo')
                    ->required()
                    ->label('Modelo'),

                # Campo Año
                TextInput::make('anio')
                    ->required()
                    ->label('Año'),

                # Campo Color
                TextInput::make('color')
                    ->required()
                    ->label('Color'),

                # Campo Kilometraje
                TextInput::make('km_registro')
                    ->required()
                    ->label('Kilometraje'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('placa')
            ->columns([
                # Campo Placa
                TextColumn::make('placa')
                    ->label('Placa')
                    ->sortable(),

                # Campo Marca
                TextColumn::make('marca')
                    ->label('Marca')
                    ->sortable(),

                # Campo Modelo
                TextColumn::make('modelo')
                    ->label('Modelo')
                    ->sortable(),

                # Campo Año
                TextColumn::make('anio')
                    ->label('Año')
                    ->sortable(),

                # Campo Kilometraje Registro
                TextColumn::make('km_registro')
                    ->label('Km Registro')
                    ->sortable()
                    ->suffix(' km'),

                # Campo Kilometraje
                TextColumn::make('kilometraje')
                    ->label('Kilometraje')
                    ->sortable()
                    ->suffix(' km'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
