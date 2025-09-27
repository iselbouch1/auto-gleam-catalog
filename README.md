# AutoStyle - Catalogue Vitrine Accessoires Auto

Application React moderne pour un catalogue vitrine d'accessoires et décorations automobiles. Interface élégante style automotive avec gestion complète des catégories, produits et recherche.

## 🚀 Fonctionnalités

### ✨ Interface Utilisateur
- **Design Automotive Premium** : Palette sombre/métallique, micro-interactions fluides
- **Responsive Mobile-First** : Optimisé pour tous les écrans
- **Navigation Intuitive** : Header avec menu catégories et recherche globale
- **Animations Fluides** : Hover effects, transitions et loading states

### 📱 Pages & Fonctionnalités
- **Accueil** : Hero section + grille catégories/nouveautés
- **Pages Catégories** : Liste paginée avec filtres et tri
- **Fiches Produits** : Galeries d'images, spécifications, produits liés
- **Recherche Globale** : Filtres avancés par catégorie, tags, etc.
- **SEO Optimisé** : Meta tags, Open Graph, données structurées

### 🛠 Architecture Technique
- **Frontend** : Vite + React 18 + TypeScript + Tailwind CSS
- **Routing** : React Router v6 avec pages dynamiques
- **Design System** : Composants réutilisables, variants customisés
- **Mock Data** : Interface prête pour API REST
- **État** : React Query pour la gestion des données

## 🏗 Structure du Projet

```
src/
├── components/           # Composants réutilisables
│   ├── ui/              # Composants shadcn personnalisés
│   ├── ProductCard.tsx   # Carte produit avec badges
│   ├── CategoryCard.tsx  # Carte catégorie avec overlay
│   ├── Header.tsx        # Navigation avec menu dropdown
│   └── Footer.tsx        # Pied de page avec liens
├── pages/               # Pages de l'application
│   ├── Home.tsx         # Page d'accueil avec hero
│   ├── CategoryPage.tsx # Liste produits par catégorie
│   └── NotFound.tsx     # Page 404 stylée
├── lib/                 # Services et utilitaires
│   └── api.ts           # Adaptateur API avec mock data
├── data/                # Données de test
│   ├── categories.json  # Catégories avec descriptions
│   └── products.json    # Produits avec specs complètes
├── types/               # Interfaces TypeScript
│   └── index.ts         # Types Category, Product, etc.
└── index.css            # Design system avec tokens
```

## 🎨 Design System

### Couleurs Automotive
- **Primary** : Noir profond élégant (`--primary`)
- **Accent** : Rouge racing (`--accent`) 
- **Secondary** : Gris métallique (`--secondary`)
- **Success/Warning/Destructive** : États avec feedback visuel

### Composants Stylés
- **Cards** : `.card-auto` avec hover effects et ombres
- **Buttons** : `.btn-automotive`, `.btn-secondary-auto`
- **Grilles** : `.product-grid`, `.category-grid` responsives
- **Badges** : `.badge-new`, `.badge-featured` avec gradients

## 🚀 Installation & Lancement

### Prérequis
- Node.js 18+ et npm
- Ou Bun pour une installation plus rapide

### Démarrage Rapide
```bash
# Installation des dépendances
npm install
# ou : bun install

# Lancement en développement
npm run dev
# ou : bun dev

# Ouvrir http://localhost:8080
```

### Commandes Disponibles
```bash
npm run dev        # Serveur de développement
npm run build      # Build de production  
npm run preview    # Aperçu du build
npm run lint       # Vérification ESLint
```

## 🔧 Configuration API

### Environnement
Copiez `.env.example` vers `.env` et configurez :

```env
# Mode mock (données locales)
VITE_USE_MOCK=true
VITE_API_BASE_URL=""

# Mode API (production)
VITE_USE_MOCK=false
VITE_API_BASE_URL=https://api.autostyle.fr
```

### Endpoints Attendus
L'adaptateur API attend ces routes REST :

```
GET /api/v1/categories           # Liste des catégories
GET /api/v1/categories/{slug}    # Catégorie par slug
GET /api/v1/products             # Liste produits (avec filtres)
GET /api/v1/products/{slug}      # Produit par slug
GET /api/v1/products/{id}/related # Produits liés
```

### Paramètres de Filtrage
- `search` : Recherche textuelle
- `category` : ID de catégorie
- `tags` : Tags séparés par virgule
- `featured=1` : Coups de cœur uniquement  
- `visible=1` : Produits visibles uniquement
- `page`, `per_page` : Pagination
- `sort_by`, `sort_order` : Tri

## 📊 Types de Données

### Category
```typescript
interface Category {
  id: string;
  name: string;
  slug: string;
  description?: string;
  image?: string;
  isVisible: boolean;
  sortOrder?: number;
}
```

### Product  
```typescript
interface Product {
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
  images: ProductImage[];
  specs?: Record<string, string | number | boolean>;
  relatedProductIds?: string[];
}
```

## 🎯 Fonctionnalités Clés

### Catalogue Vitrine
- **Pas de prix ni panier** - Vitrine uniquement
- **Catégories hiérarchiques** avec images et descriptions
- **Produits avec galeries** d'images haute résolution
- **Système de tags** pour filtrage avancé
- **Badges produits** (Nouveau, Coup de cœur)

### UX/Accessibilité
- **Navigation clavier** complète
- **Alt text** automatique pour les images
- **Contrastes optimisés** pour lisibilité
- **Responsive design** mobile-first
- **Loading states** avec skeletons

### SEO & Performance
- **Meta tags** dynamiques par page
- **Open Graph** et Twitter Cards
- **Données structurées** JSON-LD
- **Images lazy loading** pour performance
- **Canonical URLs** pour référencement

## 🚦 Roadmap

### V1 (Actuelle)
- ✅ Catalogue vitrine complet
- ✅ Design system automotive
- ✅ Navigation et recherche
- ✅ SEO optimisé

### V2 (Prochaine)
- 🔄 Page produit détaillée avec zoom
- 🔄 Filtres latéraux avancés
- 🔄 Pagination infinie
- 🔄 Partage social

### V3 (Future)
- 📋 Système de favoris
- 🌐 Internationalisation (i18n)
- 📊 Analytics et suivi
- 🔍 Recherche intelligente

## 📝 Notes Techniques

- **Pas de backend** - Frontend pur avec mock data
- **TypeScript strict** pour la robustesse
- **ESLint + Prettier** pour la qualité du code
- **Composants modulaires** facilement réutilisables
- **Design tokens** centralisés dans index.css

## 🎨 Branding

**AutoStyle** représente l'élégance automotive avec :
- Logo moderne dans header/footer
- Palette couleurs inspirée automobile
- Typographie sans-serif lisible
- Micro-interactions soignées
- Esthétique haut de gamme

---

**Développé avec ❤️ pour les passionnés d'automobile**