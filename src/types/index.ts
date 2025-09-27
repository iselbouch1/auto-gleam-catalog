// Types pour le catalogue d'accessoires auto

export interface Category {
  id: string;
  name: string;
  slug: string;
  description?: string;
  parentId?: string;
  image?: string;
  isVisible: boolean;
  sortOrder?: number;
}

export interface ProductImage {
  url: string;
  alt?: string;
  isCover?: boolean;
}

export interface Product {
  id: string;
  name: string;
  slug: string;
  shortDescription?: string;
  description?: string;
  categoryIds: string[];
  tags?: string[];
  isVisible: boolean;
  isFeatured?: boolean;
  isNew?: boolean;
  sortOrder?: number;
  images: ProductImage[];
  specs?: Record<string, string | number | boolean>;
  relatedProductIds?: string[];
}

export interface SearchFilters {
  search?: string;
  category?: string;
  tags?: string[];
  featured?: boolean;
  visible?: boolean;
  page?: number;
  perPage?: number;
  sortBy?: 'name' | 'recent' | 'featured';
  sortOrder?: 'asc' | 'desc';
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  page: number;
  perPage: number;
  totalPages: number;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  error?: string;
}