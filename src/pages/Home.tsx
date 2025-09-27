import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Category, Product } from '@/types';
import { categoriesService, productsService } from '@/lib/api';
import { ProductCard } from '@/components/ProductCard';
import { CategoryCard } from '@/components/CategoryCard';
import { Button } from '@/components/ui/button';
import { ArrowRight, Sparkles, TrendingUp } from 'lucide-react';

const Home = () => {
  const [categories, setCategories] = useState<Category[]>([]);
  const [featuredProducts, setFeaturedProducts] = useState<Product[]>([]);
  const [newProducts, setNewProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadData = async () => {
      try {
        const [categoriesData, featuredData, newData] = await Promise.all([
          categoriesService.getAll(),
          productsService.getFeatured(),
          productsService.getNew()
        ]);

        setCategories(categoriesData);
        setFeaturedProducts(featuredData.slice(0, 4));
        setNewProducts(newData.slice(0, 4));
      } catch (error) {
        console.error('Erreur lors du chargement des données:', error);
      } finally {
        setLoading(false);
      }
    };

    loadData();
  }, []);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center space-y-4">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-accent mx-auto"></div>
          <p className="text-muted-foreground">Chargement...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-to-br from-primary via-secondary to-primary py-20 lg:py-32">
        <div className="container mx-auto px-4 relative z-10">
          <div className="max-w-4xl mx-auto text-center text-primary-foreground">
            <h1 className="text-4xl md:text-6xl font-bold mb-6 leading-tight">
              Personnalisez Votre
              <span className="block text-accent">Automobile</span>
            </h1>
            <p className="text-xl md:text-2xl mb-8 text-primary-foreground/90">
              Découvrez notre catalogue premium d'accessoires et décorations 
              pour transformer votre véhicule en chef-d'œuvre unique
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button asChild className="btn-automotive text-lg px-8 py-4">
                <Link to="/categories/eclairage">
                  <Sparkles className="w-5 h-5 mr-2" />
                  Découvrir l'éclairage
                </Link>
              </Button>
              <Button asChild variant="outline" className="btn-secondary-auto text-lg px-8 py-4">
                <Link to="/categories/interieur">
                  Aménager l'intérieur
                  <ArrowRight className="w-5 h-5 ml-2" />
                </Link>
              </Button>
            </div>
          </div>
        </div>
        
        {/* Decorative elements */}
        <div className="absolute top-1/4 left-1/4 w-32 h-32 bg-accent/20 rounded-full blur-3xl"></div>
        <div className="absolute bottom-1/4 right-1/4 w-48 h-48 bg-accent/10 rounded-full blur-3xl"></div>
      </section>

      {/* Categories Section */}
      <section className="py-16 lg:py-24">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">
              Explorez Nos Catégories
            </h2>
            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
              De l'éclairage haute technologie aux accessoires de confort, 
              trouvez tout ce dont vous avez besoin pour votre véhicule
            </p>
          </div>
          
          <div className="category-grid">
            {categories.map((category) => (
              <CategoryCard key={category.id} category={category} />
            ))}
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section className="py-16 lg:py-24 bg-muted/30">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between mb-12">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-4 flex items-center">
                <TrendingUp className="w-8 h-8 text-accent mr-3" />
                Coups de Cœur
              </h2>
              <p className="text-xl text-muted-foreground">
                Nos produits les plus appréciés par nos clients
              </p>
            </div>
            <Button asChild variant="outline" className="hidden md:flex">
              <Link to="/search?featured=true">
                Voir tout
                <ArrowRight className="w-4 h-4 ml-2" />
              </Link>
            </Button>
          </div>
          
          <div className="product-grid">
            {featuredProducts.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
          
          <div className="text-center mt-8 md:hidden">
            <Button asChild variant="outline">
              <Link to="/search?featured=true">
                Voir tous les coups de cœur
                <ArrowRight className="w-4 h-4 ml-2" />
              </Link>
            </Button>
          </div>
        </div>
      </section>

      {/* New Products */}
      <section className="py-16 lg:py-24">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between mb-12">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-4 flex items-center">
                <Sparkles className="w-8 h-8 text-accent mr-3" />
                Nouveautés
              </h2>
              <p className="text-xl text-muted-foreground">
                Les derniers arrivages pour rester à la pointe de la tendance
              </p>
            </div>
            <Button asChild variant="outline" className="hidden md:flex">
              <Link to="/search?new=true">
                Voir tout
                <ArrowRight className="w-4 h-4 ml-2" />
              </Link>
            </Button>
          </div>
          
          <div className="product-grid">
            {newProducts.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
          
          <div className="text-center mt-8 md:hidden">
            <Button asChild variant="outline">
              <Link to="/search?new=true">
                Voir toutes les nouveautés
                <ArrowRight className="w-4 h-4 ml-2" />
              </Link>
            </Button>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 lg:py-24 card-hero mx-4 lg:mx-8 rounded-[var(--radius-xl)] mb-16">
        <div className="container mx-auto px-4 text-center text-primary-foreground">
          <h2 className="text-3xl md:text-4xl font-bold mb-4">
            Une Question ? Un Projet Spécifique ?
          </h2>
          <p className="text-xl mb-8 text-primary-foreground/90 max-w-2xl mx-auto">
            Notre équipe d'experts est là pour vous conseiller et vous accompagner 
            dans la personnalisation de votre véhicule
          </p>
          <Button asChild className="btn-automotive text-lg px-8 py-4">
            <a href="mailto:contact@autostyle.fr">
              Nous contacter
              <ArrowRight className="w-5 h-5 ml-2" />
            </a>
          </Button>
        </div>
      </section>
    </div>
  );
};

export default Home;