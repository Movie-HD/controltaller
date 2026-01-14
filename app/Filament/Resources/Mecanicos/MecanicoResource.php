<?php

namespace App\Filament\Resources\Mecanicos;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Mecanicos\Pages\ListMecanicos;
use App\Filament\Resources\Mecanicos\Pages\CreateMecanico;
use App\Filament\Resources\Mecanicos\Pages\EditMecanico;
use App\Filament\Resources\MecanicoResource\Pages;
use App\Filament\Resources\MecanicoResource\RelationManagers;
use App\Models\Mecanico;
use Filament\Forms;
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

    #protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-hand-raised';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->label('Nombre'),
                Select::make('empresa_id')
                    ->required()
                    ->label('Empresa')
                    ->relationship('empresa', 'nombre') # Asi obtenemos la rela el nombre de la empresa.
                    ->preload(), # Agregamos eso para que cargue los datos del select.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') # Ordenar por fecha de creación
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMecanicos::route('/'),
            'create' => CreateMecanico::route('/create'),
            'edit' => EditMecanico::route('/{record}/edit'),
        ];
    }
}
