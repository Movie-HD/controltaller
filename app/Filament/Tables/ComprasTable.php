<?php
// app/Filament/Tables/ComprasTable.php

namespace App\Filament\Tables;

use App\Models\Compra;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ComprasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) use ($table) {
                $args = $table->getArguments();
                if ($clienteId = $args['cliente_id'] ?? null) {
                    $query->where('cliente_id', $clienteId);
                }
                return $query;
            })
            ->columns([
                TextColumn::make('fecha_compra')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->formatStateUsing(fn($state) => strtoupper($state ?? 'N/A'))
                    ->color(fn($state) => match ($state) {
                        'efectivo' => 'gray',
                        'tarjeta' => 'blue',
                        'yape' => 'purple',
                        'plin' => 'pink',
                        'transferencia' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('vendedor.name')
                    ->label('Vendedor')
                    ->searchable()
                    ->toggleable()
                    ->default('N/A'),

                TextColumn::make('estado_cliente_en_compra')
                    ->label('Estado Cliente')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state ?? 'N/A'))
                    ->color(fn($state) => match ($state) {
                        'primerizo' => 'success',
                        'recurrente' => 'warning',
                        'vip' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('notas')
                    ->label('Notas')
                    ->limit(50)
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Sin notas'),
            ])
            ->filters([
                SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                        'yape' => 'Yape',
                        'plin' => 'Plin',
                        'transferencia' => 'Transferencia',
                    ])
                    ->multiple(),

                SelectFilter::make('estado_cliente_en_compra')
                    ->label('Estado del Cliente')
                    ->options([
                        'primerizo' => 'Primerizo',
                        'recurrente' => 'Recurrente',
                        'vip' => 'VIP',
                    ])
                    ->multiple(),

                Filter::make('monto_mayor_100')
                    ->label('Monto > S/ 100')
                    ->query(fn(Builder $query) => $query->where('monto', '>', 100)),

                Filter::make('monto_mayor_200')
                    ->label('Monto > S/ 200')
                    ->query(fn(Builder $query) => $query->where('monto', '>', 200)),

                Filter::make('ultimo_mes')
                    ->label('Último Mes')
                    ->query(
                        fn(Builder $query) =>
                        $query->where('fecha_compra', '>=', now()->subMonth())
                    ),

                Filter::make('ultimos_3_meses')
                    ->label('Últimos 3 Meses')
                    ->query(
                        fn(Builder $query) =>
                        $query->where('fecha_compra', '>=', now()->subMonths(3))
                    ),
            ])
            ->defaultSort('fecha_compra', 'desc')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10);
    }
}
