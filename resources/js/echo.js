import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY || process.env.VITE_PUSHER_APP_KEY || process.env.PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER || process.env.VITE_PUSHER_APP_CLUSTER || process.env.PUSHER_APP_CLUSTER,
    forceTLS: true,
    // si vous utilisez laravel-websockets en local sans TLS, adaptez host/port/wsHost/wsPort
    // wsHost: window.location.hostname,
    // wsPort: 6001,
    // disableStats: true,
});
