<?php
// app/Filament/Pages/ClientesBoard.php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\Compra;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Relaticle\Flowforge\Board;
use Relaticle\Flowforge\BoardPage;
use Relaticle\Flowforge\Column;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;

// ✅ ACCIONES CORRECTAS
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Collection;

class ClientesBoard extends BoardPage
{
    protected static ?int $navigationSort = 1;
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Kanban Clientes';
    protected static ?string $title = 'Gestión de Clientes';

    public function board(Board $board): Board
    {
        return $board
            ->query(Cliente::query()->with('compras'))
            ->columnIdentifier('estado')
            ->positionIdentifier('position')
            ->recordTitleAttribute('nombre')
            ->columns([
                Column::make('curioso')
                    ->label('📱 Curiosos')
                    ->color('blue'),

                Column::make('primerizo')
                    ->label('📦 Primerizos')
                    ->color('green'),

                Column::make('recurrente')
                    ->label('⭐ Recurrentes')
                    ->color('purple'),

                Column::make('vip')
                    ->label('👑 VIPs')
                    ->color('amber'),

                Column::make('frio')
                    ->label('❄️ Fríos')
                    ->color('gray'),
            ])

            // ✅ CONTENIDO DE TARJETAS
            ->cardSchema(fn(Schema $schema) => $schema->components([
                TextEntry::make('telefono')
                    ->icon('heroicon-o-phone')
                    ->color('gray'),

                TextEntry::make('origen')
                    ->badge()
                    ->formatStateUsing(
                        fn($state) =>
                        $state === 'curioso_convertido' ? '🏷️ Ex-Curioso' : '🏷️ Directo'
                    )
                    ->color(
                        fn($state) =>
                        $state === 'curioso_convertido' ? 'info' : 'success'
                    ),

                \Filament\Schemas\Components\FusedGroup::make([
                    TextEntry::make('total_compras')
                        ->hiddenLabel()
                        ->suffix(' compras')
                        ->icon('heroicon-o-shopping-cart'),

                    TextEntry::make('ingreso_total_generado')
                        ->hiddenLabel()
                        ->money('PEN')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success'),
                ])
                    ->visible(fn(Cliente $record) => $record->total_compras > 0)
                    #->label('Ingreso')
                    ->extraAttributes(['class' => 'rebelde'])
                    ->afterLabel(\Filament\Schemas\Components\Html::make(<<<'HTML'
                        <style>
                            .rebelde .fi-sc.fi-grid{
                                grid-template-columns: 1fr 1fr;
                                background-color: transparent;
                                box-shadow: none;
                                @media (width <= 1023px) {
                                    width: auto;
                                }
                            }
                            .fi-size-sm.fi-in-text-item.fi-wrapped.fi-in-text {
                                align-items: center;
                                display: flex;
                                gap: 3px;
                            }
                        </style>
                    HTML)),

                TextEntry::make('dias_sin_comprar')
                    ->label('Sin comprar')
                    ->suffix(' días')
                    ->icon('heroicon-o-clock')
                    ->color(fn($state) => match (true) {
                        $state > 30 => 'danger',
                        $state > 15 => 'warning',
                        default => 'gray'
                    })
                    ->visible(
                        fn(Cliente $record) =>
                        $record->estado !== 'curioso' && $record->dias_sin_comprar > 0
                    ),

                TextEntry::make('etiqueta_riesgo')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn($state) => '⚠️ ' . strtoupper(str_replace('_', ' ', $state)))
                    ->visible(fn(Cliente $record) => $record->etiqueta_riesgo !== 'ninguno'),
            ]))

            // ✅ ACCIONES EN TARJETAS
            ->cardActions([
                // 👁️ VER PERFIL COMPLETO - USANDO FORM EN VEZ DE INFOLIST

                // 👁️ VER PERFIL COMPLETO - CON REPEATER
                Action::make('view')
                    ->label('Ver Perfil')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn(Cliente $record) => "📋 Perfil de {$record->nombre}")
                    ->modalWidth('5xl')
                    ->fillForm(fn(Cliente $record): array => [
                        'nombre' => $record->nombre,
                        'telefono' => $record->telefono,
                        'email' => $record->email,
                        'origen' => $record->origen_label,
                        'estado' => ucfirst($record->estado),
                        'total_compras' => $record->total_compras,
                        'ingreso_total' => $record->ingreso_total_generado,
                        'ticket_promedio' => $record->ticket_promedio,
                        'compras_mes' => $record->compras_ultimo_mes,
                        'dias_sin_comprar' => $record->dias_sin_comprar,
                        'fecha_primera_compra' => $record->fecha_primera_compra?->format('d/m/Y'),
                        'fecha_ultima_compra' => $record->fecha_ultima_compra?->format('d/m/Y'),
                        'etiqueta_riesgo' => $record->etiqueta_riesgo !== 'ninguno'
                            ? '⚠️ ' . strtoupper(str_replace('_', ' ', $record->etiqueta_riesgo))
                            : 'Sin riesgo',
                        'notas' => $record->notas,
                        'cliente_id' => $record->id, # Para el ModalTableSelect de Compras.
                    ])
                    ->form([
                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        // SECCIÓN: INFORMACIÓN BÁSICA
                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        \Filament\Schemas\Components\Section::make('Información del Cliente')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre Completo')
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('📱')
                                    ->tel(),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('📧')
                                    ->visible(fn($get) => !empty($get('email'))),

                                Forms\Components\TextInput::make('origen')
                                    ->label('Origen')
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('estado')
                                    ->label('Estado Actual')
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('etiqueta_riesgo')
                                    ->label('Nivel de Riesgo')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn($get) => $get('etiqueta_riesgo') !== 'Sin riesgo'),
                            ])
                            ->columns(3),

                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        // SECCIÓN: MÉTRICAS COMERCIALES
                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        \Filament\Schemas\Components\Section::make('📊 Métricas Comerciales')
                            ->schema([
                                Forms\Components\TextInput::make('total_compras')
                                    ->label('Total Compras')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->suffix('compras')
                                    ->numeric(),

                                Forms\Components\TextInput::make('compras_mes')
                                    ->label('Compras este Mes')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->suffix('compras')
                                    ->numeric(),

                                Forms\Components\TextInput::make('ingreso_total')
                                    ->label('Ingreso Total Generado')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('S/')
                                    ->numeric()
                                    ->formatStateUsing(fn($state) => number_format($state, 2)),

                                Forms\Components\TextInput::make('ticket_promedio')
                                    ->label('Ticket Promedio')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('S/')
                                    ->numeric()
                                    ->formatStateUsing(fn($state) => number_format($state, 2)),

                                Forms\Components\TextInput::make('dias_sin_comprar')
                                    ->label('Días sin Comprar')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->suffix('días')
                                    ->numeric()
                                    ->extraInputAttributes(fn($state) => [
                                        'class' => $state > 30 ? 'text-red-600 font-bold' : ($state > 15 ? 'text-orange-600 font-semibold' : ''),
                                    ])
                                    ->visible(fn($get) => $get('dias_sin_comprar') > 0),
                            ])
                            ->columns(3)
                            ->collapsible(),

                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        // SECCIÓN: FECHAS IMPORTANTES
                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        \Filament\Schemas\Components\Section::make('📅 Fechas Importantes')
                            ->schema([
                                Forms\Components\TextInput::make('fecha_primera_compra')
                                    ->label('Primera Compra')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn($get) => !empty($get('fecha_primera_compra'))),

                                Forms\Components\TextInput::make('fecha_ultima_compra')
                                    ->label('Última Compra')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn($get) => !empty($get('fecha_ultima_compra'))),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->visible(fn($get) => !empty($get('fecha_primera_compra'))),

                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        // SECCIÓN: HISTORIAL DE COMPRAS - ✅ CON MODALTABLESELECT
                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        \Filament\Schemas\Components\Section::make('🛒 Historial de Compras')
                            ->schema([
                                Forms\Components\ModalTableSelect::make('compras')
                                    ->relationship('compras', 'id')
                                    ->tableConfiguration(\App\Filament\Tables\ComprasTable::class)
                                    ->tableArguments(fn(Get $get): array => [
                                        'cliente_id' => $get('cliente_id'),
                                    ])
                                    ->multiple()
                                    ->label('')
                                    ->selectAction(
                                        fn(\Filament\Actions\Action $action) => $action
                                            ->label('🔍 Ver Historial Completo')
                                            ->modalHeading('📊 Historial de Compras')
                                            ->modalDescription(
                                                fn(Cliente $record) =>
                                                "Total de compras: {$record->compras->count()} | Ingreso generado: S/ " . number_format($record->ingreso_total_generado, 2)
                                            )
                                            ->modalWidth('7xl')
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Cerrar')
                                            ->color('info')
                                            ->icon('heroicon-o-magnifying-glass-plus')
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn(Compra $record): string =>
                                        sprintf(
                                            'S/ %s | %s | %s',
                                            number_format($record->monto, 2),
                                            $record->fecha_compra->format('d/m/Y H:i'),
                                            strtoupper($record->metodo_pago ?? 'N/A')
                                        )
                                    )
                                    ->badge()
                                    ->badgeColor('success')
                                    ->dehydrated(false)
                                    ->helperText('Click en el botón para ver todas las compras con filtros y búsqueda')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->description(
                                fn(Cliente $record) =>
                                $record->compras->count() > 0
                                ? "🎯 {$record->compras->count()} compras registradas"
                                : 'Sin compras registradas'
                            ),

                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        // SECCIÓN: NOTAS
                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        \Filament\Schemas\Components\Section::make('📝 Notas')
                            ->schema([
                                Forms\Components\Textarea::make('notas')
                                    ->label('')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->rows(3)
                                    ->placeholder('Sin notas'),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->visible(fn($get) => !empty($get('notas'))),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                // 🛒 REGISTRAR COMPRA
                Action::make('registrar_compra')
                    ->label('Nueva Compra')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(fn(Cliente $record) => $record->estado !== 'curioso')
                    ->form([
                        Forms\Components\TextInput::make('monto')
                            ->label('Monto (S/)')
                            ->numeric()
                            ->required()
                            ->prefix('S/')
                            ->minValue(0.01)
                            ->step(0.01),

                        Forms\Components\Select::make('metodo_pago')
                            ->label('Método de Pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'tarjeta' => 'Tarjeta',
                                'yape' => 'Yape',
                                'plin' => 'Plin',
                                'transferencia' => 'Transferencia',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->rows(2)
                            ->placeholder('Ej: Compró 2 productos X, pidió factura'),
                    ])
                    ->action(function (Cliente $record, array $data) {
                        Compra::create([
                            'cliente_id' => $record->id,
                            'monto' => $data['monto'],
                            'metodo_pago' => $data['metodo_pago'],
                            'notas' => $data['notas'] ?? null,
                            'vendedor_id' => auth()->id(),
                            'estado_cliente_en_compra' => $record->estado,
                            'fecha_compra' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Compra registrada')
                            ->body("S/ {$data['monto']} registrado para {$record->nombre}")
                            ->send();
                    }),

                // 💬 ENVIAR WHATSAPP
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(
                        fn(Cliente $record) =>
                        "https://wa.me/51{$record->telefono}?text=" . urlencode("Hola {$record->nombre}, ")
                    )
                    ->openUrlInNewTab(),

                // ✏️ EDITAR
                EditAction::make()
                    ->model(Cliente::class)
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('telefono')
                            ->tel()
                            ->required()
                            ->unique('clientes', 'telefono', ignoreRecord: true),

                        Forms\Components\TextInput::make('email')
                            ->email(),

                        Forms\Components\Select::make('origen')
                            ->options([
                                'directo' => '🏷️ Directo',
                                'curioso_convertido' => '🏷️ Curioso Convertido',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('notas')
                            ->rows(3)
                            ->placeholder('Notas internas sobre el cliente...'),
                    ]),

                // 🗑️ ELIMINAR
                DeleteAction::make()
                    ->model(Cliente::class)
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar cliente')
                    ->modalDescription(
                        fn(Cliente $record) =>
                        "¿Estás seguro de eliminar a {$record->nombre}? Esta acción no se puede deshacer."
                    ),
            ])

            // ✅ ACCIONES EN COLUMNAS - SIN USAR $column
            ->columnActions([
                CreateAction::make()
                    ->label('Agregar')
                    ->model(Cliente::class)
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Juan Pérez'),

                        Forms\Components\TextInput::make('telefono')
                            ->tel()
                            ->required()
                            ->unique('clientes', 'telefono')
                            ->placeholder('987654321')
                            ->prefix('+51'),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->placeholder('juan@example.com'),

                        Forms\Components\Select::make('origen')
                            ->label('Origen del Cliente')
                            ->options([
                                'directo' => '🏷️ Compró Directo',
                                'curioso_convertido' => '🏷️ Era Curioso',
                            ])
                            ->required()
                            ->default('directo')
                            ->helperText('¿Cómo llegó este cliente?'),

                        Forms\Components\Textarea::make('notas')
                            ->rows(3)
                            ->placeholder('Ej: Preguntó por productos X, le interesan ofertas...'),
                    ])
                    ->mutateFormDataUsing(function (array $data, array $arguments): array {
                        // ✅ Ahora $arguments['column'] SÍ está disponible aquí
                        if (isset($arguments['column'])) {
                            $data['estado'] = $arguments['column'];
                            $data['position'] = $this->getBoardPositionInColumn($arguments['column']);
                            $data['fecha_primera_visita'] = now();
                        }
                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Cliente creado')
                            ->body('El cliente ha sido agregado al Kanban.')
                    ),
            ])

            // 🔍 BÚSQUEDA
            ->searchable(['nombre', 'telefono', 'email'])

            // 🎯 FILTROS
            ->filters([
                SelectFilter::make('origen')
                    ->label('Origen')
                    ->options([
                        'directo' => '🏷️ Directos',
                        'curioso_convertido' => '🏷️ Ex-Curiosos',
                    ]),

                SelectFilter::make('etiqueta_riesgo')
                    ->label('En Riesgo')
                    ->options([
                        'churn_risk_x1' => '⚠️ Riesgo x1',
                        'churn_risk_x2' => '⚠️ Riesgo x2',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] ?? false) {
                            $query->where('etiqueta_riesgo', $state['value']);
                        }
                    }),
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
