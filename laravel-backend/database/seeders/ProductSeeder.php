<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Kit LED Intérieur Premium RGB',
                'short_description' => 'Éclairage LED complet pour habitacle avec télécommande',
                'description' => '<p>Transformez l\'ambiance de votre véhicule avec ce kit LED premium. 8 bandes LED RGB avec télécommande, 16 millions de couleurs disponibles, installation plug-and-play.</p><ul><li>Compatible avec la plupart des véhicules via prise 12V</li><li>Télécommande infrarouge incluse</li><li>Installation sans outils</li></ul>',
                'is_featured' => true,
                'specs' => [
                    'Tension' => '12V DC',
                    'Longueur' => '8 bandes de 30cm',
                    'Couleurs' => '16 millions',
                    'Télécommande' => 'Infrarouge incluse',
                    'Installation' => 'Plug & Play',
                    'Garantie' => '2 ans'
                ],
                'categories' => ['eclairage', 'led-interieur'],
                'tags' => ['LED', 'RGB', 'Premium', 'Télécommande'],
            ],
            [
                'name' => 'Housse Siège Sport Racing Noir/Rouge',
                'short_description' => 'Housses sièges aspect cuir avec surpiqûres racing',
                'description' => '<p>Donnez un look sportif à votre intérieur avec ces housses siège racing. Matière simili-cuir haute qualité, surpiqûres contrastées, ajustement parfait.</p><p>Protège vos sièges tout en améliorant le confort et l\'esthétique.</p>',
                'is_featured' => false,
                'specs' => [
                    'Matière' => 'Simili-cuir premium',
                    'Couleur' => 'Noir/Rouge',
                    'Compatibilité' => 'Sièges avant universels',
                    'Entretien' => 'Lavable à l\'éponge',
                    'Installation' => 'Élastiques et sangles',
                    'Garantie' => '1 an'
                ],
                'categories' => ['interieur', 'housses-sieges'],
                'tags' => ['Racing', 'Cuir', 'Sport', 'Protection'],
            ],
            [
                'name' => 'Tapis Sol Caoutchouc Premium Noir',
                'short_description' => 'Tapis de sol en caoutchouc haute résistance, bords surélevés',
                'description' => '<p>Protection optimale de votre habitacle avec ces tapis caoutchouc premium. Bords surélevés anti-débordement, surface antidérapante, découpe sur-mesure selon le modèle de véhicule.</p><p>Résistant aux intempéries et facile d\'entretien.</p>',
                'is_featured' => true,
                'specs' => [
                    'Matière' => 'Caoutchouc TPE',
                    'Couleur' => 'Noir',
                    'Épaisseur' => '5mm',
                    'Bords' => 'Surélevés 15mm',
                    'Compatibilité' => 'Sur-mesure par modèle',
                    'Garantie' => '3 ans'
                ],
                'categories' => ['interieur', 'tapis-sol'],
                'tags' => ['Protection', 'Caoutchouc', 'Sur-mesure', 'Premium'],
            ],
            [
                'name' => 'Film Adhésif Carbone 3D Noir',
                'short_description' => 'Film adhésif aspect carbone 3D, texture réaliste',
                'description' => '<p>Personnalisez votre véhicule avec ce film carbone 3D ultra-réaliste. Texture fibres de carbone authentique, adhésif haute performance, résistant UV et intempéries.</p><p>Application sans bulles, repositionnable pendant la pose.</p>',
                'is_featured' => false,
                'specs' => [
                    'Dimensions' => '30cm x 152cm',
                    'Épaisseur' => '0.18mm',
                    'Finition' => 'Mate carbone 3D',
                    'Adhésif' => 'Acrylique repositionnable',
                    'Résistance' => 'UV et intempéries',
                    'Garantie' => '2 ans'
                ],
                'categories' => ['exterieur', 'stickers-decoration'],
                'tags' => ['Carbone', '3D', 'Tuning', 'Adhésif'],
            ],
            [
                'name' => 'Support Smartphone Magnétique Universel',
                'short_description' => 'Support magnétique universel pour grille d\'aération',
                'description' => '<p>Fixation sécurisée de votre smartphone avec ce support magnétique ultra-puissant. Rotation 360°, fixation grille d\'aération, aimants néodyme haute force.</p><p>Compatible tous smartphones avec plaque métallique fournie.</p>',
                'is_featured' => true,
                'specs' => [
                    'Fixation' => 'Grille d\'aération',
                    'Rotation' => '360 degrés',
                    'Aimants' => 'Néodyme N52',
                    'Compatibilité' => 'Smartphones 4-7 pouces',
                    'Matériaux' => 'ABS + Aluminium',
                    'Garantie' => '1 an'
                ],
                'categories' => ['audio-multimedia', 'supports-chargeurs', 'confort'],
                'tags' => ['Magnétique', 'Universel', 'Rotation', 'Sécurisé'],
            ],
            [
                'name' => 'Caméra de Recul HD Vision Nocturne',
                'short_description' => 'Caméra de recul HD avec vision nocturne et ligne de guidage',
                'description' => '<p>Stationnez en toute sécurité avec cette caméra HD haute définition. Vision nocturne infrarouge, lignes de guidage dynamiques, écran 4.3 pouces inclus.</p><p>Installation facile, étanche IP68, activation automatique marche arrière.</p>',
                'is_featured' => true,
                'specs' => [
                    'Résolution' => '1280x720 HD',
                    'Écran' => '4.3 pouces LCD',
                    'Vision nocturne' => 'LED infrarouge',
                    'Étanchéité' => 'IP68',
                    'Angle' => '170 degrés',
                    'Garantie' => '2 ans'
                ],
                'categories' => ['securite', 'cameras-recul', 'audio-multimedia'],
                'tags' => ['HD', 'Vision nocturne', 'Sécurité', 'Étanche'],
            ],
        ];

        foreach ($products as $productData) {
            $categoryNames = $productData['categories'];
            $tags = $productData['tags'];
            unset($productData['categories'], $productData['tags']);

            $product = Product::create($productData);

            // Attach categories
            $categories = Category::whereIn('slug', $categoryNames)->get();
            $product->categories()->attach($categories->pluck('id'));

            // Attach tags
            $product->attachTags($tags);
        }
    }
}