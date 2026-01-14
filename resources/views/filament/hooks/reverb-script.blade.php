@if(config('broadcasting.default') === 'reverb')
<script type="module">
    import Echo from 'laravel-echo';
    import Pusher from 'pusher-js';

    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ config('reverb.apps.0.key') }}',
        wsHost: '{{ config('reverb.apps.0.host') }}',
        wsPort: {{ config('reverb.apps.0.port') }},
        wssPort: {{ config('reverb.apps.0.port') }},
        forceTLS: false,
        encrypted: false,
        enabledTransports: ['ws', 'wss'],
    });

    console.log('✅ Laravel Echo conectado con Reverb');
</script>
@endif
