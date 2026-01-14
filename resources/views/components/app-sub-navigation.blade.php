@props([
    'navigation' => [],
])

<div {{ $attributes->merge(['class' => 'flex-1 px-4 overflow-x-auto no-scrollbar']) }}>
    <ul class="flex items-center gap-x-1">
        @foreach ($navigation as $group)
            @if ($group->isActive()) {{-- Solo mostramos los ítems de la "App" activa --}}
                @foreach ($group->getItems() as $parentItem)
                    {{-- Renderizamos el ítem principal (ej: Productos) --}}
                    <x-filament-panels::topbar.item
                        :active="$parentItem->isActive()"
                        :icon="$parentItem->getIcon()"
                        :url="$parentItem->getUrl()"
                    >
                        {{ $parentItem->getLabel() }}
                    </x-filament-panels::topbar.item>
                @endforeach
            @endif
        @endforeach
    </ul>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .fi-topbar{ padding: 0 5px;}
    input.fi-input{
        @media (width <= 640px) {
            width: 40px;
            transition: 1s ease;
            &:focus{
                width: auto;
            }
        }
    }
    .fi-sidebar-group.fi-collapsible:not(.fi-active), .fi-sidebar-group-btn{
    
    }
</style>