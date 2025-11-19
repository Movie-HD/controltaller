<?php

namespace App\Filament\Resources\Clientes;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Clientes\RelationManagers\VehiculosRelationManager;
use App\Filament\Resources\Clientes\RelationManagers\ReparacionesRelationManager;
use App\Filament\Resources\Clientes\Pages\ListClientes;
use App\Filament\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Resources\Clientes\Pages\EditCliente;
use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput; # Agregar si es un Input [Form]
use Filament\Forms\Components\Select; # Agregar si es un Select [Form]
use Filament\Tables\Columns\TextColumn; # Agregar si es un Column [Table]
use Filament\Forms\Components\Hidden; # Agregar si es un Hidden [Form]

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->label('Nombre'),
                TextInput::make('telefono')
                    ->required()
                    ->label('Teléfono'),
                Hidden::make('empresa_id')
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(function ($record) {
                return static::getUrl('edit', ['record' => $record->id]) . '?relation=0';
            })
            ->defaultSort('created_at', 'desc') # Ordenar por fecha de creación
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre'),
                TextColumn::make('telefono')
                    ->label('Teléfono'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
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
            VehiculosRelationManager::class,
            ReparacionesRelationManager::class,
            # php artisan make:filament-relation-manager ClienteResource vehiculos placa
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
            'edit' => EditCliente::route('/{record}/edit'),
        ];
    }
}
