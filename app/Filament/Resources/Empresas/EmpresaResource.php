<?php

namespace App\Filament\Resources\Empresas;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Empresas\Pages\ListEmpresas;
use App\Filament\Resources\Empresas\Pages\CreateEmpresa;
use App\Filament\Resources\Empresas\Pages\EditEmpresa;
use App\Filament\Resources\EmpresaResource\Pages;
use App\Filament\Resources\EmpresaResource\RelationManagers;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    #protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión';
    protected static ?string $navigationLabel = 'Empresa';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->label('Nombre de la Empresa'),
                TextInput::make('direccion')
                    ->required()
                    ->label('Dirección'),
                TextInput::make('telefono')
                    ->required()
                    ->label('Teléfono'),
                TextInput::make('correo')
                    ->email()
                    ->required()
                    ->label('Correo Electrónico'),
                TextInput::make('ruc')
                    ->label('RUC')
                    ->maxLength(11),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre'),
                TextColumn::make('direccion')
                    ->label('Dirección'),
                TextColumn::make('telefono')
                    ->label('Teléfono'),
                TextColumn::make('correo')
                    ->label('Correo Electrónico'),
                TextColumn::make('ruc')
                    ->label('RUC')
                    ->sortable(),
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
            'index' => ListEmpresas::route('/'),
            'create' => CreateEmpresa::route('/create'),
            'edit' => EditEmpresa::route('/{record}/edit'),
        ];
    }
}
