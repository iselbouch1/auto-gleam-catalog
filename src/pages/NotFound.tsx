import { Link } from "react-router-dom";
import { useEffect } from "react";

const NotFound = () => {
  useEffect(() => {
    console.error("404 Error: User attempted to access non-existent route:", location.pathname);
  }, []);

  return (
    <div className="min-h-screen flex items-center justify-center bg-background">
      <div className="text-center max-w-md mx-auto px-4">
        <div className="card-auto p-8">
          <div className="w-20 h-20 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <span className="text-4xl font-bold text-accent">404</span>
          </div>
          
          <h1 className="text-3xl font-bold mb-4 text-foreground">
            Page introuvable
          </h1>
          
          <p className="text-muted-foreground mb-8">
            Oops ! La page que vous recherchez n'existe pas ou a été déplacée.
          </p>
          
          <div className="space-y-4">
            <Link 
              to="/" 
              className="btn-automotive inline-flex items-center px-6 py-3 rounded-lg font-semibold transition-all hover:scale-105"
            >
              Retour à l'accueil
            </Link>
            
            <div className="flex justify-center space-x-4 text-sm">
              <Link 
                to="/categories/eclairage" 
                className="text-accent hover:text-accent-hover transition-colors"
              >
                Éclairage
              </Link>
              <Link 
                to="/categories/interieur" 
                className="text-accent hover:text-accent-hover transition-colors"
              >
                Intérieur  
              </Link>
              <Link 
                to="/categories/exterieur" 
                className="text-accent hover:text-accent-hover transition-colors"
              >
                Extérieur
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default NotFound;
