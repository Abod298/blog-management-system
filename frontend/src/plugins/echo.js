import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_PUSHER_APP_KEY || 'ygknubpljaa3ni6pobly',
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || '', 
  forceTLS: false, 
  wsHost: import.meta.env.VITE_PUSHER_HOST || '127.0.0.1',
  wsPort: import.meta.env.VITE_PUSHER_PORT || 8080,
  wssPort: import.meta.env.VITE_PUSHER_PORT || 8080,
  disableStats: true,
  enabledTransports: ['ws', 'wss'],
});

export default echo;