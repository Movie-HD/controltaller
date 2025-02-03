<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Reparacion;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EstadisticasStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = "full";

    public function getHeading(): string
    {
        return "Estadísticas de los Últimos 7 Días";
    }

    protected function getStats(): array
    {
        return [
            Stat::make("Total Clientes", Cliente::count())
                ->description("Número total de clientes registrados")
                ->descriptionIcon("heroicon-m-users")
                ->color("success")
                ->chart($this->getLast7DaysCounts(Cliente::class)),

            Stat::make("Total Vehículos", Vehiculo::count())
                ->description("Número total de vehículos")
                ->descriptionIcon("heroicon-s-squares-2x2")
                ->color("warning")
                ->chart($this->getLast7DaysCounts(Vehiculo::class)),

            Stat::make("Total Reparaciones", Reparacion::count())
                ->description("Número total de reparaciones")
                ->descriptionIcon("heroicon-m-wrench")
                ->color("info")
                ->chart($this->getLast7DaysCounts(Reparacion::class)),

            Stat::make(
                "Beneficio Total",
                "S/. " . number_format(Reparacion::sum("precio"), 2)
            )
                ->description("Suma total de reparaciones")
                ->descriptionIcon("heroicon-m-currency-dollar")
                ->color("success")
                ->chart($this->getLast7DaysEarnings()),
        ];
    }

    /**
     * Obtiene el conteo de registros en los últimos 7 días para un modelo dado.
     */
    private function getLast7DaysCounts($model)
    {
        return collect(range(6, 0))
            ->map(function ($daysAgo) use ($model) {
                return $model
                    ::whereDate("created_at", Carbon::now()->subDays($daysAgo))
                    ->count();
            })
            ->toArray();
    }

    /**
     * Obtiene el beneficio total de reparaciones en los últimos 7 días.
     */
    private function getLast7DaysEarnings()
    {
        return collect(range(6, 0))
            ->map(function ($daysAgo) {
                return Reparacion::whereDate(
                    "created_at",
                    Carbon::now()->subDays($daysAgo)
                )->sum("precio");
            })
            ->toArray();
    }
}
