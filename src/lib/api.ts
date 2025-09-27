import { Category, Product, SearchFilters, PaginatedResponse, ApiResponse } from '@/types';
import categoriesData from '@/data/categories.json';
import productsData from '@/data/products.json';

// Configuration API
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '';
const USE_MOCK_DATA = import.meta.env.VITE_USE_MOCK !== 'false';

// Fonction utilitaire pour les requêtes API
async function fetchJson<T>(path: string, params?: URLSearchParams): Promise<ApiResponse<T>> {
  try {
    const url = `${API_BASE_URL}${path}${params ? `?${params}` : ''}`;
    const response = await fetch(url);
    
    if (!response.ok) {
      throw new Error(`API Error: ${response.status}`);
    }
    
    const data = await response.json();
    return { success: true, data };
  } catch (error) {
    return { 
      success: false, 
      data: {} as T, 
      error: error instanceof Error ? error.message : 'Unknown error' 
    };
  }
}

// Service de gestion des catégories
export const categoriesService = {
  async getAll(): Promise<Category[]> {
    if (USE_MOCK_DATA) {
      return categoriesData as Category[];
    }
    
    const response = await fetchJson<Category[]>('/api/v1/categories');
    return response.success ? response.data : [];
  },

  async getBySlug(slug: string): Promise<Category | null> {
    if (USE_MOCK_DATA) {
      const category = categoriesData.find(c => c.slug === slug);
      return category as Category || null;
    }
    
    const response = await fetchJson<Category>(`/api/v1/categories/${slug}`);
    return response.success ? response.data : null;
  }
};

// Service de gestion des produits
export const productsService = {
  async getAll(filters?: SearchFilters): Promise<PaginatedResponse<Product>> {
    if (USE_MOCK_DATA) {
      let filteredProducts = productsData as Product[];
      
      // Appliquer les filtres
      if (filters?.search) {
        const searchLower = filters.search.toLowerCase();
        filteredProducts = filteredProducts.filter(p => 
          p.name.toLowerCase().includes(searchLower) ||
          p.shortDescription?.toLowerCase().includes(searchLower) ||
          p.tags?.some(tag => tag.toLowerCase().includes(searchLower))
        );
      }
      
      if (filters?.category) {
        filteredProducts = filteredProducts.filter(p => 
          p.categoryIds.includes(filters.category!)
        );
      }
      
      if (filters?.tags && filters.tags.length > 0) {
        filteredProducts = filteredProducts.filter(p => 
          filters.tags!.some(tag => p.tags?.includes(tag))
        );
      }
      
      if (filters?.featured !== undefined) {
        filteredProducts = filteredProducts.filter(p => p.isFeatured === filters.featured);
      }
      
      if (filters?.visible !== undefined) {
        filteredProducts = filteredProducts.filter(p => p.isVisible === filters.visible);
      }
      
      // Tri
      if (filters?.sortBy) {
        filteredProducts.sort((a, b) => {
          const order = filters.sortOrder === 'desc' ? -1 : 1;
          
          switch (filters.sortBy) {
            case 'name':
              return a.name.localeCompare(b.name) * order;
            case 'recent':
              return (a.sortOrder || 0) - (b.sortOrder || 0) * order;
            case 'featured':
              return (Number(b.isFeatured) - Number(a.isFeatured)) * order;
            default:
              return 0;
          }
        });
      }
      
      // Pagination
      const page = filters?.page || 1;
      const perPage = filters?.perPage || 12;
      const total = filteredProducts.length;
      const totalPages = Math.ceil(total / perPage);
      const startIndex = (page - 1) * perPage;
      const endIndex = startIndex + perPage;
      
      return {
        data: filteredProducts.slice(startIndex, endIndex),
        total,
        page,
        perPage,
        totalPages
      };
    }
    
    // API réelle
    const params = new URLSearchParams();
    if (filters?.search) params.set('search', filters.search);
    if (filters?.category) params.set('category', filters.category);
    if (filters?.tags) params.set('tags', filters.tags.join(','));
    if (filters?.featured !== undefined) params.set('featured', filters.featured ? '1' : '0');
    if (filters?.visible !== undefined) params.set('visible', filters.visible ? '1' : '0');
    if (filters?.page) params.set('page', filters.page.toString());
    if (filters?.perPage) params.set('per_page', filters.perPage.toString());
    if (filters?.sortBy) params.set('sort_by', filters.sortBy);
    if (filters?.sortOrder) params.set('sort_order', filters.sortOrder);
    
    const response = await fetchJson<PaginatedResponse<Product>>('/api/v1/products', params);
    return response.success ? response.data : { data: [], total: 0, page: 1, perPage: 12, totalPages: 0 };
  },

  async getBySlug(slug: string): Promise<Product | null> {
    if (USE_MOCK_DATA) {
      const product = productsData.find(p => p.slug === slug);
      return product as Product || null;
    }
    
    const response = await fetchJson<Product>(`/api/v1/products/${slug}`);
    return response.success ? response.data : null;
  },

  async getFeatured(): Promise<Product[]> {
    const response = await this.getAll({ featured: true, visible: true });
    return response.data;
  },

  async getNew(): Promise<Product[]> {
    if (USE_MOCK_DATA) {
      return productsData.filter(p => p.isNew && p.isVisible) as Product[];
    }
    
    const response = await this.getAll({ visible: true });
    return response.data.filter(p => p.isNew);
  },

  async getRelated(productId: string): Promise<Product[]> {
    if (USE_MOCK_DATA) {
      const product = productsData.find(p => p.id === productId) as Product;
      if (!product?.relatedProductIds) return [];
      
      return productsData.filter(p => 
        product.relatedProductIds!.includes(p.id) && p.isVisible
      ) as Product[];
    }
    
    const response = await fetchJson<Product[]>(`/api/v1/products/${productId}/related`);
    return response.success ? response.data : [];
  }
};