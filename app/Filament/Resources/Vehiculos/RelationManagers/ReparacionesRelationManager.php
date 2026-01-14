<?php

namespace App\Filament\Resources\Vehiculos\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
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
use Filament\Forms\Components\TextInput; # Agregar si es un Input [Form]
use Filament\Forms\Components\Select; # Agregar si es un Select [Form]
use Filament\Forms\Components\Textarea; # Agregar si es un Textarea [Form]
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Hidden; # Agregar si es un Hidden [Form]
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get; # Agregar para funcion de opciones
use Illuminate\Support\Collection; # Agregar para funcion de opciones
use Filament\Forms\Set; # Agregar para afterStateUpdated [Form]
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Vehiculos\Pages\EditVehiculo;

class ReparacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'reparaciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                # Campo Descripción
                TextArea::make('descripcion')
                    ->label('Descripcion de Reparación')
                    ->required()
                    ->columnSpan([
                        'default' => 2, // Por defecto, ocupa 1 columna en dispositivos pequeños.
                        'sm' => 2, // Ocupa 2 columnas en dispositivos grandes.
                    ]),

                # Campo Servicios
                TextArea::make('servicios')
                    ->label('Repuestos Cambiados')
                    ->required()
                    ->columnSpan(['default' => 2, 'sm' => 2]),

                # Campo Notas
                TextArea::make('notas')
                    ->label('Notas Adicionales')
                    ->nullable()
                    ->columnSpan(['default' => 2, 'sm' => 2]),

                # Campo Kilometraje
                TextInput::make('kilometraje')
                    ->label('Kilometraje')
                    ->numeric()
                    ->required()
                    ->suffix('km'),

                # Campo Precio
                TextInput::make('precio')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('S/.')
                    # Permiso personalizado para mostrar el precio Filament-SHIELD.
                    ->visible(fn() => auth()->user()->can('ViewPrecioReparacion:Vehiculo'))
                    ->nullable(),

                # Campo Cliente
                Hidden::make('cliente_id')
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->cliente_id), // Usa el cliente del vehículo relacionado

                # Campo Vehículo
                Hidden::make('vehiculo_id')
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->id), // Usa el ID del vehículo relacionado

                # Campo Empresa
                Hidden::make('empresa_id')
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->cliente->empresa_id), // Usa la empresa del cliente relacionado

                # Campo Mecánicos (Many-to-Many)
                Select::make('mecanicos')
                    ->label('Mecánicos')
                    ->relationship('mecanicos', 'nombre')
                    # Falta definir como json para que sea multiselect.
                    ->multiple()
                    ->required()
                    ->live()
                    ->searchable()
                    ->preload()

                    # SubModal para crear un nuevo Mecánico
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required(),
                        Select::make('empresa_id')
                            ->label('')
                            ->relationship('empresa', 'nombre')
                            ->default(1)
                            ->extraAttributes(['style' => 'display:none;']),
                    ])
                    ->columnSpan(['default' => 2, 'sm' => 2]),

                # Repeater para agregar los servicios como oportunidades.
                Repeater::make('oportunidades')
                    ->table([
                        TableColumn::make('Servicio'),
                        TableColumn::make('¿Cuando?'),
                    ])
                    ->compact()
                    ->schema([
                        TextInput::make('servicio')
                            ->placeholder('Servicio')
                            ->required(),
                        DatePicker::make('fecha')
                            ->required()
                    ])
                    ->columnSpan(['default' => 2, 'sm' => 2])
                    #->hiddenLabel()
                    ->label('Oportunidades')
                    ->addActionLabel('Nuevo Servicio'),
            ])
            ->columns(['default' => 2, 'sm' => 2]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') # Ordenar por fecha de creación
            ->recordTitleAttribute('descripcion')
            ->columns([
                # Campo Descripción
                TextColumn::make('descripcion')
                    ->label('Descripcion de Reparación'),

                # Campo Precio
                TextColumn::make('precio')
                    ->prefix('S/. ')
                    # Permiso personalizado para mostrar el precio Filament-SHIELD.
                    ->visible(fn() => auth()->user()->can('ViewPrecioReparacion:Vehiculo')),

                # Campo Servicios
                TextColumn::make('servicios')
                    ->label('Repuestos Cambiados'),

                # Campo Notas
                TextColumn::make('notas')
                    ->extraAttributes(['class' => 'truncate max-w-xs']),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalHeading('Ver Reparación')
                        ->slideOver(),
                    EditAction::make()
                        ->modalHeading('Editar Reparación')
                        ->slideOver()
                        ->color('primary'),
                    DeleteAction::make(),
                ])
            ])

            # Comprobamos si estamos en View o Edit para que el enlace de la tabla de cada registro nos abra el modal EditAction o ViewAction
            # con [recordAction] para modales y [recordUrl] cuando te saca de la página actual y te lleva a una URL completamente nueva.
            # Para eso necesitamos importar Model y EditVehiculo(Pagina de Edición de Vehiculo)
            ->recordAction(function (?Model $record): string {

                // $this->pageClass ya es un string con el nombre completo de la clase
                $currentPageClass = $this->pageClass;

                // Comparamos el nombre de la clase actual con el nombre de la clase EditVehiculo
                if ($currentPageClass === EditVehiculo::class) {
                    // Si estamos en la página de edición, devuelve 'edit'
                    // para abrir el EditAction como modal.
                    return 'edit';
                }

                // Por defecto, devuelve 'view' para abrir el ViewAction como modal.
                return 'view';
            })
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

}
