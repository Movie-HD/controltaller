<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Reparacion;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class EstadisticasStats extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 2;
    }

    public function getHeading(): string
    {
        return "Estadísticas por Rango de Fechas";
    }

    protected function getStats(): array
    {
        $preset = $this->filters['presetRange'] ?? 'last_7_days';

        $startDate = null;
        $endDate = now()->endOfDay();

        switch ($preset) {
            case 'today':
                $startDate = now()->startOfDay();
                break;

            case 'yesterday':
                $startDate = now()->subDay()->startOfDay();
                $endDate = now()->subDay()->endOfDay();
                break;

            case 'last_7_days':
                $startDate = now()->subDays(6)->startOfDay(); // incluye hoy
                break;

            case 'last_30_days':
                $startDate = now()->subDays(29)->startOfDay();
                break;

            case 'this_month':
                $startDate = now()->startOfMonth();
                break;

            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;

            case 'custom':
                $startDate = !is_null($this->filters['startDate'] ?? null)
                    ? Carbon::parse($this->filters['startDate'])->startOfDay()
                    : now()->subDays(6)->startOfDay();

                $endDate = !is_null($this->filters['endDate'] ?? null)
                    ? Carbon::parse($this->filters['endDate'])->endOfDay()
                    : now()->endOfDay();
                break;

            default:
                $startDate = now()->subDays(6)->startOfDay();
                break;
        }

        return [
            Stat::make(
                "Clientes",
                Cliente::whereBetween("created_at", [$startDate, $endDate])->count()
            )
                ->description("Nuevos clientes")
                ->descriptionIcon("heroicon-m-users")
                ->color("success")
                ->chart($this->getDailyCounts(Cliente::class, $startDate, $endDate)),

            Stat::make(
                "Vehículos",
                Vehiculo::whereBetween("created_at", [$startDate, $endDate])->count()
            )
                ->description("Nuevos vehículos")
                ->descriptionIcon("heroicon-s-squares-2x2")
                ->color("warning")
                ->chart($this->getDailyCounts(Vehiculo::class, $startDate, $endDate)),

            Stat::make(
                "Reparaciones",
                Reparacion::whereBetween("created_at", [$startDate, $endDate])->count()
            )
                ->description("Nuevas reparaciones")
                ->descriptionIcon("heroicon-m-wrench")
                ->color("info")
                ->chart($this->getDailyCounts(Reparacion::class, $startDate, $endDate)),

            Stat::make(
                "Beneficio",
                "S/. " . number_format(
                    Reparacion::whereBetween("created_at", [$startDate, $endDate])->sum("precio"),
                    2
                )
            )
                ->description("Total reparaciones")
                ->descriptionIcon("heroicon-m-currency-dollar")
                ->color("success")
                ->chart($this->getDailyEarnings($startDate, $endDate)),
        ];
    }


    private function getDailyCounts($model, Carbon $start, Carbon $end): array
    {
        if ($start->gt($end)) {
            return [];
        }

        $days = (int) $start->diffInDays($end);

        // Log temporal para depuración
        \Log::debug('getDailyCounts', [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'days' => $days,
            'days_type' => gettype($days)
        ]);

        if ($days === 0) {
            return [ $model::whereDate('created_at', $start)->count() ];
        }

        if ($days > 0) {
            return collect(range(0, $days))
                ->map(fn ($i) => $model::whereDate('created_at', $start->copy()->addDays($i))->count())
                ->toArray();
        }

        return [];

    }

    private function getDailyEarnings(Carbon $start, Carbon $end): array
    {
        if ($start->gt($end)) {
            return [];
        }

        $days = (int) $start->diffInDays($end);

        if ($days === 0) {
            return [ Reparacion::whereDate('created_at', $start)->sum('precio') ];
        }

        if ($days > 0) {
            return collect(range(0, $days))
                ->map(fn ($i) => Reparacion::whereDate('created_at', $start->copy()->addDays($i))->sum('precio'))
                ->toArray();
        }

        return [];

    }

}
