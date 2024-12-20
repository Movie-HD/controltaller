<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MecanicoResource\Pages;
use App\Filament\Resources\MecanicoResource\RelationManagers;
use App\Models\Mecanico;
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

class MecanicoResource extends Resource
{
    protected static ?string $model = Mecanico::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Administrativo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->label('Nombre'),
                Select::make('empresa_id')
                    ->required()
                    ->label('Empresa')
                    ->relationship('empresa', 'nombre') # Asi obtenemos la rela el nombre de la empresa.
                    ->preload(), # Agregamos eso para que cargue los datos del select.
                Select::make('sucursal_id')
                    ->required()
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre') # Asi obtenemos la rela el nombre de la empresa.
                    ->preload(), # Agregamos eso para que cargue los datos del select.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre'),
                TextColumn::make('empresa.nombre')
                    ->label('Empresa'),
                TextColumn::make('sucursal.nombre') # Para no mostrar el empresa_id, debemos poner el nombre del metodo de la relacion y luego el campo que queremos mostrar.
                    ->label('Sucursal'),
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
            'index' => Pages\ListMecanicos::route('/'),
            'create' => Pages\CreateMecanico::route('/create'),
            'edit' => Pages\EditMecanico::route('/{record}/edit'),
        ];
    }
}
