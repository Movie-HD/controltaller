<?php

namespace App\Filament\Widgets;

use App\Models\Reparacion;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class BeneficiosChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Beneficios';
    protected static ?int $sort = 3;

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
        $endDate = Carbon::now();
        $startDate = match ($this->filter) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'quarter' => Carbon::now()->subMonths(3),
            'semester' => Carbon::now()->subMonths(6),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };

        $dates = collect();
        $currentDate = $startDate->copy()->startOfDay();

        while ($currentDate <= $endDate) {
            $dates->push($currentDate->copy());
            $currentDate->addDay();
        }

        $groupBy = $this->filter === 'week' ? 'd M' : 'M Y';
        $labels = $dates->map(fn($date) => $date->format($groupBy))->unique();

        // Datos de beneficios
        $beneficiosData = Reparacion::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($item) => Carbon::parse($item->created_at)->format($groupBy))
            ->map(fn($group) => $group->sum('precio'));

        $labels = $labels->unique()->values();
        $emptyData = array_fill_keys($labels->toArray(), 0);
        $beneficiosData = array_merge($emptyData, $beneficiosData->toArray());

        return [
            'datasets' => [
                [
                    'label' => 'Beneficios',
                    'data' => array_values($beneficiosData),
                    'borderColor' => '#9C27B0',
                    'backgroundColor' => 'rgba(156, 39, 176, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Cambiado a línea para mejor visualización de tendencias
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
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
