import { Link } from 'react-router-dom';
import { Category } from '@/types';

interface CategoryCardProps {
  category: Category;
  className?: string;
}

export const CategoryCard = ({ category, className = '' }: CategoryCardProps) => {
  return (
    <Link 
      to={`/categories/${category.slug}`}
      className={`block card-auto hover-lift group ${className}`}
    >
      <div className="relative overflow-hidden rounded-t-xl">
        <img
          src={category.image || '/placeholder.svg'}
          alt={category.name}
          className="w-full h-32 md:h-40 object-cover transition-transform duration-300 group-hover:scale-105"
          loading="lazy"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent" />
        
        <div className="absolute bottom-3 left-3 right-3">
          <h3 className="font-semibold text-lg text-white mb-1">
            {category.name}
          </h3>
          {category.description && (
            <p className="text-white/90 text-sm line-clamp-2">
              {category.description}
            </p>
          )}
        </div>
      </div>
      
      <div className="p-4">
        <div className="flex items-center justify-between">
          <span className="text-accent font-semibold">
            Voir la catégorie
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