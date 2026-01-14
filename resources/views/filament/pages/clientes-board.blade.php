<x-filament-panels::page>
    {{-- Load Vite assets (includes Laravel Echo) --}}
    @vite(['resources/js/app.js'])

    <div x-data="kanbanRealtime()" x-init="init()" class="relative">
        {{-- Notificación flotante --}}
        <div x-show="notification.show" x-transition
            class="fixed top-4 right-4 z-50 px-6 py-3 text-white bg-blue-500 rounded-lg shadow-lg">
            <p x-text="notification.message"></p>
        </div>

        {{-- Contenido del Kanban --}}
        {{ $this->board }}
    </div>

    @push('scripts')
        <script>
            function kanbanRealtime() {
                return {
                    notification: {
                        show: false,
                        message: ''
                    },

                    init() {
                        console.log('🔴 Iniciando Reverb para Kanban...');

                        // Verificar que Echo esté disponible
                        if (!window.Echo) {
                            console.error('❌ Laravel Echo no está inicializado');
                            return;
                        }

                        // Escuchar movimientos de reparaciones
                        window.Echo.channel('kanban-reparaciones')
                            .listen('.reparacion.movida', (event) => {
                                console.log('📦 Reparación movida:', event);
                                this.showNotification(
                                    `${event.usuario} movió la reparación ${event.placa} de ${event.estado_anterior} → ${event.estado_nuevo}`
                                );

                                // Recargar el componente Livewire
                                this.$wire.$refresh();
                            })
                            .listen('.reparacion.actualizada', (event) => {
                                console.log('✏️ Reparación actualizada:', event);

                                if (event.accion === 'creada') {
                                    this.showNotification(`${event.usuario} registró una nueva reparación: ${event.placa}`);
                                } else if (event.accion === 'eliminada') {
                                    this.showNotification(`${event.usuario} eliminó la reparación: ${event.placa}`);
                                } else if (event.accion === 'actualizada') {
                                    this.showNotification(`${event.usuario} actualizó la reparación: ${event.placa}`);
                                }

                                this.$wire.$refresh();
                            });

                        console.log('✅ Kanban Real-time inicializado correctamente');
                    },

                    showNotification(message) {
                        this.notification.message = message;
                        this.notification.show = true;

                        setTimeout(() => {
                            this.notification.show = false;
                        }, 5000);
                    }
                }
            }
        </script>
    @endpush
</x-filament-panels::page>