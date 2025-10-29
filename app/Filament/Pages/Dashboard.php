<?php

namespace App\Filament\Pages;

use Filament\View\PanelsRenderHook;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Select;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    
    public function booted(): void
    {
        Filament::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn () => Blade::render('
                <style>
                    .tita {

                        & .fi-section-content {
                            padding: clamp(4px, calc(2px + .8vw), 20px);
                        }

                    }
                </style>
            ')
        );
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('presetRange')
                            ->label('Rango rápido')
                            ->options([
                                'today' => 'Hoy',
                                'yesterday' => 'Ayer',
                                'last_7_days' => 'Últimos 7 días',
                                'last_30_days' => 'Últimos 30 días',
                                'this_month' => 'Este mes',
                                'last_month' => 'Mes pasado',
                                'custom' => 'Personalizado',
                            ])
                            ->default('last_7_days'),

                        DatePicker::make('startDate')
                            ->label('Desde')
                            ->default(now()->subDays(7))
                            ->visible(fn ($get) => $get('presetRange') === 'custom')
                            ->maxDate(fn ($get) => $get('presetRange') === 'custom' ? $get('endDate') : null),
                        
                        DatePicker::make('endDate')
                            ->label('Hasta')
                            ->default(now())
                            ->visible(fn ($get) => $get('presetRange') === 'custom')
                            ->minDate(fn ($get) => $get('presetRange') === 'custom' ? $get('startDate') : null)
                            ->maxDate(now()),
                    ])
                    ->columns(3)
                    ->extraAttributes([
                        'class' => 'tita', // clases Tailwind o personalizadas
                    ]),
            ]);
    }
}
