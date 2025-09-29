import Pusher from 'pusher-js';
import { queryClient } from './queryClient';

// Check if Pusher environment variables are available
const pusherAppKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherHost = import.meta.env.VITE_PUSHER_HOST;

let pusher: Pusher | null = null;

// Only initialize Pusher if we have the required environment variables
if (pusherAppKey && pusherHost) {
  pusher = new Pusher(pusherAppKey, {
    wsHost: pusherHost,
    wsPort: parseInt(import.meta.env.VITE_PUSHER_PORT || '8080'),
    wssPort: parseInt(import.meta.env.VITE_PUSHER_PORT || '8080'),
    forceTLS: import.meta.env.VITE_PUSHER_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
    cluster: '',
  });
} else {
  console.warn('Pusher environment variables not found. Real-time sync disabled.');
}

export const setupRealtimeSync = () => {
  if (!pusher) {
    console.log('Real-time sync not available - Pusher not initialized');
    return;
  }

  const productsChannel = pusher.subscribe('products');
  
  productsChannel.bind('product.created', (data: any) => {
    queryClient.invalidateQueries({ queryKey: ['products'] });
  });

  productsChannel.bind('product.updated', (data: any) => {
    queryClient.invalidateQueries({ queryKey: ['products'] });
    queryClient.invalidateQueries({ queryKey: ['product', data.product.slug] });
  });

  const categoriesChannel = pusher.subscribe('categories');
  
  categoriesChannel.bind('category.updated', (data: any) => {
    queryClient.invalidateQueries({ queryKey: ['categories'] });
  });
};

export { pusher };