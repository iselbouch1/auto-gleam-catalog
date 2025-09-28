import Pusher from 'pusher-js';
import { queryClient } from './queryClient';

const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
  wsHost: import.meta.env.VITE_PUSHER_HOST,
  wsPort: parseInt(import.meta.env.VITE_PUSHER_PORT || '8080'),
  wssPort: parseInt(import.meta.env.VITE_PUSHER_PORT || '8080'),
  forceTLS: import.meta.env.VITE_PUSHER_SCHEME === 'https',
  enabledTransports: ['ws', 'wss'],
  cluster: '',
});

export const setupRealtimeSync = () => {
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