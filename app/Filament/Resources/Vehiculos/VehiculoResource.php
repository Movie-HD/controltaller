<?php

namespace App\Filament\Resources\Vehiculos;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Vehiculos\RelationManagers\ReparacionesRelationManager;
use App\Filament\Resources\Vehiculos\RelationManagers\WhatsappmensajesRelationManager;
use App\Filament\Resources\Vehiculos\Pages\ListVehiculos;
use App\Filament\Resources\Vehiculos\Pages\CreateVehiculo;
use App\Filament\Resources\Vehiculos\Pages\EditVehiculo;
use App\Filament\Resources\Vehiculos\Pages\ViewVehiculo;
use App\Filament\Resources\VehiculoResource\Pages;
use App\Filament\Resources\VehiculoResource\RelationManagers;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput; # Agregar si es un Input [Form]
use Filament\Forms\Components\Select; # Agregar si es un Select [Form]
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Hidden;

class VehiculoResource extends Resource
{
    protected static ?string $model = Vehiculo::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-squares-2x2';

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->components([
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
                    ->label('Cliente')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->getSearchResultsUsing(function (string $search): array {
                        session(['cliente_search' => $search]);

                        return Cliente::whereRaw("REPLACE(nombre, ' ', '') LIKE ?", ["%".str_replace(' ', '', $search)."%"])
                            ->limit(50)
                            ->pluck('nombre', 'id')
                            ->toArray();
                    })
                    ->getOptionLabelUsing(fn ($value): ?string => Cliente::find($value)?->nombre)
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->default(fn () => session('cliente_search', '')), // Usa el valor buscado
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->required(),
                        Hidden::make('empresa_id')
                            ->default(1),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        // Verifica los datos recibidos antes de crear el cliente

                        return Cliente::create($data)->id;
                    })
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
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

    public static function getRelations(): array
    {
        return [
            ReparacionesRelationManager::class,
            WhatsappmensajesRelationManager::class,
            # php artisan make:filament-relation-manager NombreResource NombreMetodoRelacion CampoRelacion
            # php artisan make:filament-relation-manager VehiculoResource reparaciones descripcion
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehiculos::route('/'),
            'create' => CreateVehiculo::route('/create'),
            'edit' => EditVehiculo::route('/{record}/edit'),
            'view' => ViewVehiculo::route('/{record}'),
        ];
    }
}
