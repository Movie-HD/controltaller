<?php
// app/Filament/Pages/ClientesBoard.php

namespace App\Filament\Pages;

use App\Models\Reparacion;
use App\Models\Vehiculo;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Relaticle\Flowforge\Board;
use Relaticle\Flowforge\BoardPage;
use Relaticle\Flowforge\Column;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

// ✅ ACCIONES CORRECTAS
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Collection;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class FlujoTallerBoard extends BoardPage
{
    use HasPageShield;
    protected static ?int $navigationSort = 1;
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Taller';
    protected static ?string $title = 'Flujo del Taller';

    public function board(Board $board): Board
    {
        return $board
            ->query(Reparacion::query()->with(['vehiculo', 'cliente']))
            ->columnIdentifier('estado')
            ->positionIdentifier('position')
            ->recordTitleAttribute('descripcion')
            ->columns([
                Column::make('recepcion')
                    ->label('� Recepción')
                    ->color('gray'),

                Column::make('diagnostico')
                    ->label('🔍 Diagnóstico')
                    ->color('warning'),

                Column::make('proceso')
                    ->label('� En Proceso')
                    ->color('blue'),

                Column::make('espera_repuestos')
                    ->label('📦 Repuestos')
                    ->color('danger'),

                Column::make('finalizado')
                    ->label('✅ Finalizado')
                    ->color('success'),

                Column::make('entregado')
                    ->label('🏁 Entregado')
                    ->color('amber'),
            ])

            // ✅ CONTENIDO DE TARJETAS
            ->cardSchema(fn(Schema $schema) => $schema->components([
                TextEntry::make('vehiculo.placa')
                    ->label('Placa')
                    ->weight('bold')
                    ->icon('heroicon-o-identification'),

                TextEntry::make('vehiculo_info')
                    ->label('Vehículo')
                    ->formatStateUsing(fn(Reparacion $record) => "{$record->vehiculo->marca} {$record->vehiculo->modelo}")
                    ->icon('heroicon-o-truck'),

                TextEntry::make('cliente.nombre')
                    ->label('Cliente')
                    ->icon('heroicon-o-user')
                    ->color('gray'),

                \Filament\Schemas\Components\FusedGroup::make([
                    TextEntry::make('precio')
                        ->money('PEN')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->visible(fn() => auth()->user()->can('ViewPrecioReparacion:Vehiculo')),

                    TextEntry::make('created_at')
                        ->since()
                        ->icon('heroicon-o-clock')
                        ->color('gray'),
                ])
                    ->extraAttributes(['class' => 'rebelde'])
                    ->afterLabel(\Filament\Schemas\Components\Html::make(<<<'HTML'
                        <style>
                            .rebelde .fi-sc.fi-grid{
                                grid-template-columns: 1fr 1fr;
                                background-color: transparent;
                                box-shadow: none;
                            }
                        </style>
                    HTML)),
            ]))

            // ✅ ACCIONES EN TARJETAS
            ->cardActions([
                Action::make('view')
                    ->label('Ver Detalles')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn(Reparacion $record) => "� Reparación: {$record->vehiculo->placa}")
                    ->modalWidth('4xl')
                    ->fillForm(fn(Reparacion $record): array => [
                        'descripcion' => $record->descripcion,
                        'vehiculo' => "{$record->vehiculo->marca} {$record->vehiculo->modelo} ({$record->vehiculo->placa})",
                        'cliente' => $record->cliente->nombre,
                        'precio' => $record->precio,
                        'notas' => $record->notas,
                    ])
                    ->form([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('vehiculo')
                                    ->label('Vehículo')
                                    ->disabled(),
                                Forms\Components\TextInput::make('cliente')
                                    ->label('Cliente')
                                    ->disabled(),
                                Forms\Components\Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->disabled()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio')
                                    ->prefix('S/')
                                    ->disabled()
                                    ->visible(fn() => auth()->user()->can('ViewPrecioReparacion:Vehiculo')),
                                Forms\Components\Textarea::make('notas')
                                    ->label('Notas')
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->modalSubmitAction(false),

                // 💬 ENVIAR WHATSAPP AL CLIENTE
                Action::make('whatsapp')
                    ->label('Avisar Cliente')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(
                        fn(Reparacion $record) =>
                        "https://wa.me/51{$record->cliente->telefono}?text=" . urlencode("Hola {$record->cliente->nombre}, te informamos que tu vehículo {$record->vehiculo->placa} se encuentra en estado: " . strtoupper($record->estado))
                    )
                    ->openUrlInNewTab(),

                // ✏️ EDITAR REPARACIÓN
                EditAction::make()
                    ->model(Reparacion::class)
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Forms\Components\Textarea::make('descripcion')
                            ->required(),
                        Forms\Components\TextInput::make('precio')
                            ->numeric()
                            ->prefix('S/')
                            ->visible(fn() => auth()->user()->can('ViewPrecioReparacion:Vehiculo')),
                        Forms\Components\Textarea::make('notas'),
                    ]),

                // 🗑️ ELIMINAR
                DeleteAction::make()
                    ->model(Reparacion::class),
            ])

            // ✅ ACCIONES EN COLUMNAS
            ->columnActions([
                CreateAction::make()
                    ->label('Nuevo')
                    ->model(Reparacion::class)
                    ->form([
                        Forms\Components\Select::make('vehiculo_id')
                            ->label('Vehículo')
                            ->relationship('vehiculo', 'placa')
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn($state, Forms\Set $set) => $set('cliente_id', Vehiculo::find($state)?->cliente_id)),

                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción de la falla')
                            ->required(),

                        Forms\Components\TextInput::make('kilometraje')
                            ->numeric()
                            ->required(),
                    ])
                    ->mutateFormDataUsing(function (array $data, array $arguments): array {
                        if (isset($arguments['column'])) {
                            $data['estado'] = $arguments['column'];
                            $data['position'] = $this->getBoardPositionInColumn($arguments['column']);
                            $data['empresa_id'] = auth()->user()->empresa_id ?? 1;
                        }
                        return $data;
                    }),
            ])

            // 🔍 BÚSQUEDA
            ->searchable(['vehiculo.placa', 'cliente.nombre', 'descripcion'])

            // 🎯 FILTROS
            ->filters([
                SelectFilter::make('mecanicos')
                    ->relationship('mecanicos', 'nombre')
                    ->multiple(),
            ])

            // 🖱️ Hacer tarjetas clickeables
            ->cardAction('view');
    }

    /**
     * Override view to use custom Blade template with real-time updates
     */
    public function getView(): string
    {
        return 'filament.pages.clientes-board';
    }

    /**
     * Hook para inyectar JavaScript personalizado
     */
    protected function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'enableRealtime' => true,
        ]);
    }

    /**
     * Scripts adicionales para escuchar Reverb
     */
    public function getExtraAlpineAttributes(): array
    {
        return [
            'x-init' => 'initRealtimeUpdates()',
        ];
    }

    /**
     * Listeners de Livewire
     */
    protected $listeners = [
        'refreshKanban' => '$refresh',
    ];

    /**
     * Hook llamado cuando se mueve una tarjeta
     */
    public function onCardMoved(string $recordId, string $targetColumn, ?string $afterRecordId = null, ?string $beforeRecordId = null): void
    {
        parent::onCardMoved($recordId, $targetColumn, $afterRecordId, $beforeRecordId);

        // El evento ya se disparará automáticamente desde el modelo
        // al detectar el cambio de 'estado'
    }
}
