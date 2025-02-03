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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Hidden;

class VehiculoResource extends Resource
{
    protected static ?string $model = Vehiculo::class;

    protected static ?string $navigationIcon = 'heroicon-s-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Datos del Vehiculo')
            ->columns([
                'default' => 2, // Por defecto, usa 1 columna para pantallas pequeñas.
                'sm' => 3, // A partir del tamaño 'sm', usa 2 columnas.
            ])
            ->schema([
                TextInput::make('placa')
                    ->required()
                    ->unique(ignoreRecord: true)
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
                TextInput::make('km_registro')
                    ->required()
                    ->label('Kilometraje')
                    ->numeric(),
                Select::make('cliente_id')
                    ->required()
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre') # Asi obtenemos la rela el nombre de la empresa.
                    ->preload() # Agregamos eso para que cargue los datos del select.
                    ->searchable()
                    ->createOptionForm([ # Agregamos esto para crear un nuevo cliente desde un modal.
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->required(),
                        Hidden::make('empresa_id')
                            ->default(1),
                    ])
                    ->columnSpan([
                        'default' => 2, // Por defecto, ocupa 1 columna en dispositivos pequeños.
                        'sm' => 3, // Ocupa 2 columnas en dispositivos grandes.
                    ]),
            ])
            #->collapsed(fn ($livewire) => $livewire->getRecord() !== null)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(function ($record) {
                return static::getUrl('view', ['record' => $record->id]) . '?activeRelationManager=0';
            })
            ->defaultSort('created_at', 'desc') # Ordenar por fecha de creación
            ->columns([
                TextColumn::make('placa')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
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
                TextColumn::make('km_registro')
                    ->label('Km Registro')
                    ->sortable()
                    ->suffix(' km'),
                TextColumn::make('kilometraje')
                    ->label('Kilometraje')
                    ->sortable()
                    ->suffix(' km'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->color('primary'),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            RelationManagers\ReparacionesRelationManager::class,
            #RelationManagers\WhatsappmensajesRelationManager::class,
            # php artisan make:filament-relation-manager NombreResource NombreMetodoRelacion CampoRelacion
            # php artisan make:filament-relation-manager VehiculoResource reparaciones descripcion
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehiculos::route('/'),
            'create' => Pages\CreateVehiculo::route('/create'),
            'edit' => Pages\EditVehiculo::route('/{record}/edit'),
            'view' => Pages\ViewVehiculo::route('/{record}'),
        ];
    }
}
