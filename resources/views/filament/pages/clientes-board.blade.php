<x-filament-panels::page>
    {{-- Load Vite assets (includes Laravel Echo) --}}
    @vite(['resources/js/app.js'])

    <div
        x-data="kanbanRealtime()"
        x-init="init()"
        class="relative"
    >
        {{-- Notificación flotante --}}
        <div
            x-show="notification.show"
            x-transition
            class="fixed top-4 right-4 z-50 px-6 py-3 text-white bg-blue-500 rounded-lg shadow-lg"
        >
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

                    // Escuchar movimientos de clientes
                    window.Echo.channel('kanban-clientes')
                        .listen('.cliente.movido', (event) => {
                            console.log('📦 Cliente movido:', event);
                            this.showNotification(
                                `${event.usuario} movió a ${event.cliente_nombre} de ${event.estado_anterior} → ${event.estado_nuevo}`
                            );

                            // Recargar el componente Livewire
                            this.$wire.$refresh();
                        })
                        .listen('.cliente.actualizado', (event) => {
                            console.log('✏️ Cliente actualizado:', event);

                            if (event.accion === 'creado') {
                                this.showNotification(`${event.usuario} creó un nuevo cliente: ${event.cliente.nombre}`);
                            } else if (event.accion === 'eliminado') {
                                this.showNotification(`${event.usuario} eliminó al cliente: ${event.cliente.nombre}`);
                            } else if (event.accion === 'actualizado') {
                                this.showNotification(`${event.usuario} actualizó al cliente: ${event.cliente.nombre}`);
                            }

                            this.$wire.$refresh();
                        })
                        .listen('.compra.registrada', (event) => {
                            console.log('💰 Nueva compra:', event);
                            this.showNotification(
                                `${event.usuario} registró S/ ${event.monto} para ${event.cliente_nombre}`
                            );

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
