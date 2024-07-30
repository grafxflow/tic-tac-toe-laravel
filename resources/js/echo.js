import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Appear in the Pusher Debug LOG but doens't work
/*
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: false
});
*/

// Doesn't Appear in the Pusher Debug LOG but does work
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.Echo.channel(`new-game-channel`)
.listen('NewGame', (event) => {
    console.log(event);
});

window.Echo.channel(`game-over`)
.listen('GameOver', (event) => {
    console.log(event);
});

/*
window.Echo.private(`order.${orderId}`)
.listen('ShippingStatusUpdated', (e) => {
    console.log(e.update);
});
*/

/*
var gameId = null;
var userId = null;

window.Echo.channel(`game-channel.${gameId}.${userId}`)
.listen('Play', (event) => {
    console.log(event);
});
*/

/*
window.Echo.channel("new-game-channel").listen("NewGame", (event) => {
    console.log(event);
});
*/
