<?php

namespace App\Filament\Resources\Clientes\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
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
use Filament\Tables\Grouping\Group;

class ReparacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'reparaciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('descripcion')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('vehiculo.placa')
                ->label('Vehículo')
                ->sortable(),
                TextColumn::make('descripcion'),
            ])
            ->groups([
                Group::make('vehiculo.placa') // ✅ Agrupa por vehículo
                    ->label('Vehículo')
                    ->collapsible()
                    ->orderQueryUsing(fn ($query, $direction) =>
                        $query->orderBy('reparacions.created_at', 'desc')
                    ),
            ])
            ->defaultGroup('vehiculo.placa')
            ->groupingSettingsHidden()
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
