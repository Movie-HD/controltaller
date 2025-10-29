<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Reparacion;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class GeneralChart extends ChartWidget
{
    protected ?string $heading = 'Estadísticas';
    protected static ?int $sort = 2;

    // Establecer filtro por defecto
    public ?string $filter = 'week';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Última semana',
            'month' => 'Último mes',
            'quarter' => 'Último trimestre',
            'semester' => 'Último semestre',
            'year' => 'Último año',
        ];
    }

    protected function getData(): array
    {
        // Determinar el rango de fechas basado en el filtro
        $endDate = Carbon::now();
        $startDate = match ($this->filter) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'quarter' => Carbon::now()->subMonths(3),
            'semester' => Carbon::now()->subMonths(6),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };

        // Crear array de fechas entre el rango seleccionado
        $dates = collect();
        $currentDate = $startDate->copy()->startOfDay();

        while ($currentDate <= $endDate) {
            $dates->push($currentDate->copy());
            $currentDate->addDay(); // Cambiado a días para más detalle
        }

        // Agrupar por días o meses según el filtro
        $groupBy = $this->filter === 'week' ? 'd M' : 'M Y';

        // Obtener las etiquetas
        $labels = $dates->map(fn ($date) => $date->format($groupBy))->unique();

        // Datos de clientes
        $clientesData = Cliente::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->created_at)->format($groupBy))
            ->map(fn ($group) => $group->count());

        // Datos de vehículos
        $vehiculosData = Vehiculo::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->created_at)->format($groupBy))
            ->map(fn ($group) => $group->count());

        // Datos de reparaciones
        $reparacionesData = Reparacion::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->created_at)->format($groupBy))
            ->map(fn ($group) => $group->count());

        // Asegurar que todas las fechas tienen valores
        $labels = $labels->unique()->values();
        $emptyData = array_fill_keys($labels->toArray(), 0);

        $clientesData = array_merge($emptyData, $clientesData->toArray());
        $vehiculosData = array_merge($emptyData, $vehiculosData->toArray());
        $reparacionesData = array_merge($emptyData, $reparacionesData->toArray());

        return [
            'datasets' => [
                [
                    'label' => 'Clientes',
                    'data' => array_values($clientesData),
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                ],
                [
                    'label' => 'Vehículos',
                    'data' => array_values($vehiculosData),
                    'borderColor' => '#FFC107',
                    'backgroundColor' => 'rgba(255, 193, 7, 0.2)',
                ],
                [
                    'label' => 'Rep.',
                    'data' => array_values($reparacionesData),
                    'borderColor' => '#2196F3',
                    'backgroundColor' => 'rgba(33, 150, 243, 0.2)',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position' => 'left',
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];
    }
}
