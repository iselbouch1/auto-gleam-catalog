import { Link } from 'react-router-dom';

export const Footer = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-primary text-primary-foreground mt-20">
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Logo & Description */}
          <div className="space-y-4">
            <div className="flex items-center space-x-2">
              <div className="bg-accent text-accent-foreground px-3 py-2 rounded-lg">
                <span className="font-bold text-lg">AutoStyle</span>
              </div>
            </div>
            <p className="text-primary-foreground/80">
              Votre spécialiste en accessoires et décorations automobiles. 
              Découvrez notre catalogue premium pour personnaliser votre véhicule.
            </p>
          </div>

          {/* Catégories */}
          <div className="space-y-4">
            <h3 className="font-semibold text-lg">Catégories</h3>
            <ul className="space-y-2">
              <li>
                <Link 
                  to="/categories/eclairage" 
                  className="text-primary-foreground/80 hover:text-accent transition-colors"
                >
                  Éclairage
                </Link>
              </li>
              <li>
                <Link 
                  to="/categories/interieur" 
                  className="text-primary-foreground/80 hover:text-accent transition-colors"
                >
                  Intérieur
                </Link>
              </li>
              <li>
                <Link 
                  to="/categories/exterieur" 
                  className="text-primary-foreground/80 hover:text-accent transition-colors"
                >
                  Extérieur
                </Link>
              </li>
              <li>
                <Link 
                  to="/categories/audio-multimedia" 
                  className="text-primary-foreground/80 hover:text-accent transition-colors"
                >
                  Audio & Multimédia
                </Link>
              </li>
            </ul>
          </div>

          {/* Liens utiles */}
          <div className="space-y-4">
            <h3 className="font-semibold text-lg">Informations</h3>
            <ul className="space-y-2">
              <li>
                <a 
                  href="#" 
                  className="text-primary-foreground/80 hover:text-accent transition-colors"
                >
                  À propos
                </a>
              </li>
              <li>
                <a 
                  href="#" 
                  className="text-primary-foreground/80 hover:text-accent transition-colors"
                >
                  Contact
                </a>
              </li>
              <li>
                <a 
                  href="#" 
                  className="text-primary-foreground/80 hover:text-accent transition-colors"
                >
                  Livraison
                </a>
              </li>
              <li>
                <a 
                  href="#" 
                  className="text-primary-foreground/80 hover:text-accent transition-colors"
                >
                  Mentions légales
                </a>
              </li>
            </ul>
          </div>

          {/* Contact */}
          <div className="space-y-4">
            <h3 className="font-semibold text-lg">Contact</h3>
            <div className="space-y-2 text-primary-foreground/80">
              <p>📧 contact@autostyle.fr</p>
              <p>📞 01 23 45 67 89</p>
              <p>📍 123 Rue de l'Automobile<br />75001 Paris, France</p>
            </div>
            
            {/* Réseaux sociaux */}
            <div className="flex space-x-4 pt-4">
              <a 
                href="#" 
                className="w-8 h-8 bg-primary-foreground/20 rounded-full flex items-center justify-center hover:bg-accent transition-colors"
              >
                <span className="text-sm">f</span>
              </a>
              <a 
                href="#" 
                className="w-8 h-8 bg-primary-foreground/20 rounded-full flex items-center justify-center hover:bg-accent transition-colors"
              >
                <span className="text-sm">@</span>
              </a>
              <a 
                href="#" 
                className="w-8 h-8 bg-primary-foreground/20 rounded-full flex items-center justify-center hover:bg-accent transition-colors"
              >
                <span className="text-sm">in</span>
              </a>
            </div>
          </div>
        </div>

        <div className="border-t border-primary-foreground/20 mt-8 pt-8 text-center">
          <p className="text-primary-foreground/60">
            © {currentYear} AutoStyle. Tous droits réservés. | Catalogue vitrine - Pas de vente en ligne
          </p>
        </div>
      </div>
    </footer>
  );
};