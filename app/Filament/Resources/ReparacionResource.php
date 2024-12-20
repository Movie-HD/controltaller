<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReparacionResource\Pages;
use App\Filament\Resources\ReparacionResource\RelationManagers;
use App\Models\Reparacion;
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

class ReparacionResource extends Resource
{
    protected static ?string $model = Reparacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('descripcion')
                    ->label('Descripción')
                    ->required(),
                TextInput::make('servicios')
                    ->label('Servicios realizados')
                    ->required(),
                TextInput::make('kilometraje')
                    ->label('Kilometraje')
                    ->numeric()
                    ->required(),
                TextInput::make('notas')
                    ->label('Notas adicionales')
                    ->nullable(),
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre') # Nombre del metodo de la relacion + campo a mostrar
                    ->required(),
                Select::make('vehiculo_id')
                    ->label('Vehículo')
                    ->relationship('vehiculo', 'placa') # Nombre del metodo de la relacion + campo a mostrar
                    ->required(),
                Select::make('empresa_id')
                    ->label('Empresa')
                    ->relationship('empresa', 'nombre') # Nombre del metodo de la relacion + campo a mostrar
                    ->required(),
                Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre') # Nombre del metodo de la relacion + campo a mostrar
                    ->required(),
                Select::make('mecanico_id')
                    ->label('Mecánico')
                    ->relationship('mecanico', 'nombre') # Nombre del metodo de la relacion + campo a mostrar
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50), # Limitamos el texto mostrado en la tabla
                TextColumn::make('servicios')
                    ->label('Servicios')
                    ->limit(50), // Limita el texto mostrado en la tabla
                TextColumn::make('kilometraje')
                    ->label('Kilometraje')
                    ->sortable(),
                TextColumn::make('notas')
                    ->label('Notas adicionales')
                    ->limit(50)
                    ->toggleable(), // Permite mostrar u ocultar esta columna
                TextColumn::make('cliente.nombre')
                    ->label('Cliente') // Ajusta 'nombre' según el campo relevante en la tabla clientes
                    ->sortable(),
                TextColumn::make('vehiculo.placa')
                    ->label('Placa del Vehículo') // Ajusta 'placa' según el campo relevante en la tabla vehículos
                    ->sortable(),
                TextColumn::make('empresa.nombre')
                    ->label('Empresa') // Ajusta 'nombre' según el campo relevante en la tabla empresas
                    ->sortable(),
                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal') // Ajusta 'nombre' según el campo relevante en la tabla sucursales
                    ->sortable(),
                TextColumn::make('mecanico.nombre')
                    ->label('Mecánico') // Ajusta 'nombre' según el campo relevante en la tabla mecánicos
                    ->sortable(),
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
            'index' => Pages\ListReparacions::route('/'),
            'create' => Pages\CreateReparacion::route('/create'),
            'edit' => Pages\EditReparacion::route('/{record}/edit'),
        ];
    }
}
