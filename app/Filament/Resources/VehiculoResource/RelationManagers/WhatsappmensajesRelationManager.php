<?php

namespace App\Filament\Resources\VehiculoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\WhatsappPlantilla;
use Filament\Forms\Components\TextInput; # Agregar si es un Input [Form]
use Filament\Forms\Components\Select; # Agregar si es un Select [Form]
use Filament\Forms\Components\Textarea; # Agregar si es un Textarea [Form]
use Filament\Tables\Columns\TextColumn; # Agregar si es un Column [Table]
use Filament\Forms\Components\DateTimePicker; # Agregar si es un DateTimePicker [Form]
use Filament\Forms\Components\Hidden; # Agregar si es un Hidden [Form]
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model; # Se agrego para cambiar el titulo
class WhatsappmensajesRelationManager extends RelationManager
{
    protected static string $relationship = 'whatsappmensajes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Mensajes de WhatsApp';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                # Campo Plantilla
                Select::make('plantilla_id')
                    ->required()
                    ->relationship('plantilla', 'nombre')
                    ->label('Tipo de mensaje')
                    ->reactive() // Reactivo para manejar cambios
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            // Obtiene el contenido de la plantilla seleccionada y lo asigna al campo 'contenido'
                            $template = WhatsappPlantilla::find($state);
                            $set('mensaje', $template ? $template->mensaje : null);
                        }
                    })

                    # SubModal para crear una nueva plantilla
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required(),
                        TextArea::make('mensaje')
                            ->label('Mensaje')
                            ->hint('Marcadores de posición: {cliente}, {marca}, {modelo}, {año}, {placa}')
                            ->required(),
                    ])
                    ->editOptionForm([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required(),
                        TextArea::make('mensaje')
                            ->label('Mensaje')
                            ->hint('Marcadores de posición: {cliente}, {marca}, {modelo}, {año}, {placa}')
                            ->required(),
                    ]),

                # Campo Mensaje
                TextArea::make('mensaje')
                    ->required()
                    ->hint('Marcadores de posición: {cliente}, {marca}, {modelo}, {año}, {placa}')
                    ->visible(fn (callable $get) => $get('plantilla_id') != null),

                # Campo Fecha Programada
                DateTimePicker::make('fecha_programada')
                    ->required(),

                # --[CAMPOS OCULTOS]--
                # Campo Cliente
                Hidden::make('cliente_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->cliente_id), // Usa el cliente del vehículo relacionado

                # Campo Vehículo
                Hidden::make('vehiculo_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id), // Usa el ID del vehículo relacionado

                # Campo Reparacion
                Hidden::make('reparacion_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->cliente->reparacion_id), // Usa la empresa del cliente relacionado
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') # Ordenar por fecha de creación
            ->recordTitleAttribute('mensaje')
            ->columns([
                Tables\Columns\TextColumn::make('mensaje'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $cliente = \App\Models\Cliente::find($data['cliente_id']);
                    $vehiculo = \App\Models\Vehiculo::find($data['vehiculo_id']); // Asegúrate de que 'vehiculo_id' esté en los datos

                    // Obtener el mensaje actual, sea personalizado o no
                    $mensajeActual = $data['mensaje'] ?? '';

                    // Reemplazar los marcadores de posición en el mensaje actual
                    $data['mensaje'] = str_replace(
                        ['{cliente}', '{marca}', '{modelo}', '{año}', '{placa}'],
                        [$cliente?->nombre ?? '', $vehiculo?->marca ?? '', $vehiculo?->modelo ?? '', $vehiculo?->anio ?? '', $vehiculo?->placa ?? ''],
                        $mensajeActual
                    );

                    return $data; // Devuelve los datos modificados
                })
                ->after(function (array $data) {
                    $cliente = \App\Models\Cliente::find($data['cliente_id']);
                    $phone_number_cliente = '51' . $cliente->telefono;

                    $fecha_programada = \Carbon\Carbon::parse($data['fecha_programada']);

                    \App\Jobs\SendWhatsAppMessage::dispatch($phone_number_cliente, $data['mensaje'])
                        ->delay($fecha_programada);
                })
                    ->slideOver(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

}
