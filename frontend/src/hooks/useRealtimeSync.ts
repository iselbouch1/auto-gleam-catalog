import { useEffect } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { pusher } from '../lib/pusher';

/**
 * Hook pour activer la synchronisation temps réel
 * Invalide automatiquement les queries React Query
 * quand des événements WebSocket sont reçus
 */
export const useRealtimeSync = () => {
  const queryClient = useQueryClient();

  useEffect(() => {
    // Channel produits
    const productsChannel = pusher.subscribe('products');
    
    const handleProductEvent = (data: any) => {
      console.log('Product event received:', data);
      
      // Invalider les queries générales
      queryClient.invalidateQueries({ queryKey: ['products'] });
      queryClient.invalidateQueries({ queryKey: ['featured-products'] });
      
      // Si c'est une mise à jour ou suppression, invalider le produit spécifique
      if (data.action === 'updated' || data.action === 'deleted') {
        queryClient.invalidateQueries({ 
          queryKey: ['product', data.product.slug] 
        });
        
        // Invalider les catégories affectées
        data.product.categories?.forEach((categorySlug: string) => {
          queryClient.invalidateQueries({ 
            queryKey: ['products', 'category', categorySlug] 
          });
        });
      }
      
      // Si suppression, supprimer complètement du cache
      if (data.action === 'deleted') {
        queryClient.removeQueries({ 
          queryKey: ['product', data.product.slug] 
        });
      }
    };

    productsChannel.bind('product.created', handleProductEvent);
    productsChannel.bind('product.updated', handleProductEvent);
    productsChannel.bind('product.deleted', handleProductEvent);

    // Channel catégories
    const categoriesChannel = pusher.subscribe('categories');
    
    const handleCategoryEvent = (data: any) => {
      console.log('Category event received:', data);
      
      // Invalider les queries catégories
      queryClient.invalidateQueries({ queryKey: ['categories'] });
      
      if (data.action === 'updated' || data.action === 'deleted') {
        queryClient.invalidateQueries({ 
          queryKey: ['category', data.category.slug] 
        });
        
        // Invalider les produits de cette catégorie
        queryClient.invalidateQueries({ 
          queryKey: ['products', 'category', data.category.slug] 
        });
      }
      
      if (data.action === 'deleted') {
        queryClient.removeQueries({ 
          queryKey: ['category', data.category.slug] 
        });
      }
    };

    categoriesChannel.bind('category.created', handleCategoryEvent);
    categoriesChannel.bind('category.updated', handleCategoryEvent);
    categoriesChannel.bind('category.deleted', handleCategoryEvent);

    // Cleanup on unmount
    return () => {
      productsChannel.unbind_all();
      categoriesChannel.unbind_all();
      pusher.unsubscribe('products');
      pusher.unsubscribe('categories');
    };
  }, [queryClient]);
};