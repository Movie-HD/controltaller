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

    protected function getColumns(): int
    {
        // Esto le dice a Filament que, en la mayoría de los casos, intente 2 columnas.
        // El ajuste para móviles lo haremos con CSS.
        return 2;
    }

    public function getHeading(): string
    {
        return "Estadísticas de los Últimos 7 Días";
    }

    protected function getStats(): array
    {
        // Define el punto de inicio para los últimos 7 días
        $startDate = Carbon::now()->subDays(7)->startOfDay(); // Inicio hace 7 días (medianoche)
        // Carbon::now()->subDays(6)->startOfDay() si quieres incluir hoy y 6 días atrás.
        // O simplemente subDays(7) si quieres 7 días completos ANTES de hoy.

        return [
            Stat::make(
                "Clientes (7 Días)", // Cambiado el título para reflejar el rango
                Cliente::where("created_at", ">=", $startDate)->count() // Solo clientes creados en los últimos 7 días
            )
                ->description("Nuevos clientes en 7 días")
                ->descriptionIcon("heroicon-m-users")
                ->color("success")
                ->chart($this->getLast7DaysCounts(Cliente::class)),

            Stat::make(
                "Vehículos (7 Días)", // Cambiado el título
                Vehiculo::where("created_at", ">=", $startDate)->count() // Solo vehículos creados en los últimos 7 días
            )
                ->description("Nuevos vehículos en 7 días")
                ->descriptionIcon("heroicon-s-squares-2x2")
                ->color("warning")
                ->chart($this->getLast7DaysCounts(Vehiculo::class)),

            Stat::make(
                "Reparaciones (7 Días)", // Cambiado el título
                Reparacion::where("created_at", ">=", $startDate)->count() // Solo reparaciones creadas en los últimos 7 días
            )
                ->description("Nuevas reparaciones en 7 días")
                ->descriptionIcon("heroicon-m-wrench")
                ->color("info")
                ->chart($this->getLast7DaysCounts(Reparacion::class)),

            Stat::make(
                "Beneficio (7 Días)", // Cambiado el título
                "S/. " .
                    number_format(
                        Reparacion::where("created_at", ">=", $startDate)->sum("precio"), // Suma de precios solo de los últimos 7 días
                        2
                    )
            )
                ->description("Suma de reparaciones en 7 días")
                ->descriptionIcon("heroicon-m-currency-dollar")
                ->color("success")
                ->chart($this->getLast7DaysEarnings()),
        ];
    }

    /**
     * Obtiene el conteo de registros en los últimos 7 días para un modelo dado.
     * (Este método ya estaba bien para el CHART)
     */
    private function getLast7DaysCounts($model)
    {
        return collect(range(6, 0))
            ->map(function ($daysAgo) use ($model) {
                // Carbon::now()->subDays($daysAgo) se refiere a la fecha exacta de hace X días a la medianoche.
                return $model
                    ::whereDate("created_at", Carbon::now()->subDays($daysAgo))
                    ->count();
            })
            ->toArray();
    }

    /**
     * Obtiene el beneficio total de reparaciones en los últimos 7 días.
     * (Este método ya estaba bien para el CHART)
     */
    private function getLast7DaysEarnings()
    {
        return collect(range(6, 0))
            ->map(function ($daysAgo) {
                // Carbon::now()->subDays($daysAgo) se refiere a la fecha exacta de hace X días a la medianoche.
                return Reparacion::whereDate(
                    "created_at",
                    Carbon::now()->subDays($daysAgo)
                )->sum("precio");
            })
            ->toArray();
    }
}
