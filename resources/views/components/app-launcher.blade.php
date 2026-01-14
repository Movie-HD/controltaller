@props([
    'navigation' => [],
])

<x-filament::dropdown placement="bottom-start" teleport width="sm">
    <x-slot name="trigger">  
        <x-filament::icon-button
            icon="heroicon-o-squares-2x2"
            icon-size="lg"
            color="gray"
            label="Aplicaciones"
            class="hover:bg-gray-100 dark:hover:bg-white/5"
        />
    </x-slot>

    <div class="grid grid-cols-3 gap-2 p-4" style="grid-template-columns: repeat(3, minmax(0, 1fr)); min-width: 320px;">
        @foreach ($navigation as $group)
            @php
                // Si el grupo no tiene etiqueta (ej: Dashboard), le asignamos "Inicio"
                $groupLabel = $group->getLabel() ?: 'Inicio'; 
                
                // Si el grupo no tiene icono, usamos una casa por defecto
                $groupIcon = $group->getIcon() ?: 'heroicon-o-home';
                
                $firstItem = $group->getItems()->first();
                $itemUrl = $firstItem?->getUrl();
            @endphp
            
            @if ($itemUrl)
                <a
                    href="{{ $itemUrl }}"
                    @class([
                        'flex flex-col items-center justify-center gap-2 rounded-xl p-3 transition',
                        'bg-primary-50 dark:bg-primary-500/10 ring-1 ring-primary-500/20' => $group->isActive(),
                        'hover:bg-gray-50 dark:hover:bg-white/5' => ! $group->isActive(),
                    ])
                >
                    <div @class([
                        'p-3 rounded-xl shadow-sm',
                        'bg-primary-500 text-white' => $group->isActive(),
                        'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' => ! $group->isActive(),
                    ])>
                        <x-filament::icon
                            :icon="$groupIcon" {{-- <-- Usamos la variable $groupIcon con fallback --}}
                            class="h-8 w-8"
                        />
                    </div>
                    <span class="text-[11px] font-bold text-center leading-tight uppercase tracking-tighter truncate w-full">
                        {{ $groupLabel }} {{-- <-- Usamos la variable $groupLabel con fallback --}}
                    </span>
                </a>
            @endif
        @endforeach
    </div>
</x-filament::dropdown>