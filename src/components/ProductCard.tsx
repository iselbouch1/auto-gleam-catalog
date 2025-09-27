import { Link } from 'react-router-dom';
import { Product } from '@/types';
import { Badge } from '@/components/ui/badge';

interface ProductCardProps {
  product: Product;
  className?: string;
}

export const ProductCard = ({ product, className = '' }: ProductCardProps) => {
  const coverImage = product.images.find(img => img.isCover) || product.images[0];
  
  return (
    <Link 
      to={`/produits/${product.slug}`}
      className={`block card-auto hover-lift ${className}`}
    >
      <div className="relative overflow-hidden rounded-t-xl">
        <img
          src={coverImage?.url || '/placeholder.svg'}
          alt={coverImage?.alt || product.name}
          className="w-full h-48 object-cover transition-transform duration-300 hover:scale-105"
          loading="lazy"
        />
        
        {/* Badges */}
        <div className="absolute top-3 left-3 flex gap-2">
          {product.isNew && (
            <Badge className="badge-new">
              Nouveau
            </Badge>
          )}
          {product.isFeatured && (
            <Badge className="badge-featured">
              Coup de cœur
            </Badge>
          )}
        </div>
      </div>
      
      <div className="p-4">
        <h3 className="font-semibold text-lg mb-2 text-card-foreground group-hover:text-accent transition-colors">
          {product.name}
        </h3>
        
        {product.shortDescription && (
          <p className="text-muted-foreground text-sm mb-3 line-clamp-2">
            {product.shortDescription}
          </p>
        )}
        
        {/* Tags */}
        {product.tags && product.tags.length > 0 && (
          <div className="flex flex-wrap gap-1 mb-3">
            {product.tags.slice(0, 3).map((tag) => (
              <span
                key={tag}
                className="text-xs px-2 py-1 bg-muted text-muted-foreground rounded-full"
              >
                {tag}
              </span>
            ))}
            {product.tags.length > 3 && (
              <span className="text-xs px-2 py-1 bg-muted text-muted-foreground rounded-full">
                +{product.tags.length - 3}
              </span>
            )}
          </div>
        )}
        
        <div className="flex items-center justify-between">
          <span className="text-accent font-semibold">
            Voir détails
          </span>
          <svg
            className="w-4 h-4 text-accent transform transition-transform group-hover:translate-x-1"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M9 5l7 7-7 7"
            />
          </svg>
        </div>
      </div>
    </Link>
  );
};