<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
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

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->label('Nombre del Cliente'),
                TextInput::make('telefono')
                    ->required()
                    ->label('Teléfono'), 
                Select::make('empresa_id')
                    ->required()
                    ->relationship('empresa', 'nombre') # Asi obtenemos la rela el nombre de la empresa.
                    ->preload() # Agregamos eso para que cargue los datos del select.
                    ->label('Empresa'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre'),
                TextColumn::make('telefono')
                    ->label('Teléfono'),
                TextColumn::make('empresa.nombre') # Para no mostrar el empresa_id, debemos poner el nombre del metodo de la relacion y luego el campo que queremos mostrar.
                    ->label('Empresa'),
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
            RelationManagers\VehiculosRelationManager::class,
            # php artisan make:filament-relation-manager ClienteResource vehiculos placa
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
