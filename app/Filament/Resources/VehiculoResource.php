<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculoResource\Pages;
use App\Filament\Resources\VehiculoResource\RelationManagers;
use App\Models\Vehiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput; # Agregar si es un Input [Form]
use Filament\Forms\Components\Select; # Agregar si es un Select [Form]
use Filament\Tables\Columns\TextColumn; # Agregar si es un Column [Table]

class VehiculoResource extends Resource
{
    protected static ?string $model = Vehiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('placa')
                    ->required()
                    ->unique()
                    ->label('Placa'),
                TextInput::make('marca')
                    ->required()
                    ->label('Marca'),
                TextInput::make('modelo')
                    ->required()
                    ->label('Modelo'),
                TextInput::make('anio')
                    ->required()
                    ->label('Año')
                    ->numeric(),
                TextInput::make('color')
                    ->required()
                    ->label('Color'),
                TextInput::make('kilometraje')
                    ->required()
                    ->label('Kilometraje')
                    ->numeric(),
                Select::make('cliente_id')
                    ->required()
                    ->label('ID del Cliente')
                    ->relationship('cliente', 'nombre') # Asi obtenemos la rela el nombre de la empresa.
                    ->preload(), # Agregamos eso para que cargue los datos del select.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('placa')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('marca')
                    ->label('Marca')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('modelo')
                    ->label('Modelo')
                    ->sortable(),
                TextColumn::make('anio')
                    ->label('Año')
                    ->sortable(),
                TextColumn::make('color')
                    ->label('Color'),
                TextColumn::make('kilometraje')
                    ->label('Kilometraje')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => number_format($state) . ' km'),
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehiculos::route('/'),
            'create' => Pages\CreateVehiculo::route('/create'),
            'edit' => Pages\EditVehiculo::route('/{record}/edit'),
        ];
    }
}
