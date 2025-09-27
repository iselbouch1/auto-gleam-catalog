<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Éclairage',
                'slug' => 'eclairage',
                'sort_order' => 1,
                'children' => [
                    ['name' => 'LED Intérieur', 'slug' => 'led-interieur'],
                    ['name' => 'Phares & Feux', 'slug' => 'phares-feux'],
                    ['name' => 'Éclairage d\'ambiance', 'slug' => 'eclairage-ambiance'],
                ]
            ],
            [
                'name' => 'Intérieur',
                'slug' => 'interieur',
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Housses & Sièges', 'slug' => 'housses-sieges'],
                    ['name' => 'Tapis de Sol', 'slug' => 'tapis-sol'],
                    ['name' => 'Tableau de Bord', 'slug' => 'tableau-bord'],
                ]
            ],
            [
                'name' => 'Extérieur',
                'slug' => 'exterieur',
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Carrosserie', 'slug' => 'carrosserie'],
                    ['name' => 'Jantes & Pneus', 'slug' => 'jantes-pneus'],
                    ['name' => 'Stickers & Décoration', 'slug' => 'stickers-decoration'],
                ]
            ],
            [
                'name' => 'Audio & Multimédia',
                'slug' => 'audio-multimedia',
                'sort_order' => 4,
                'children' => [
                    ['name' => 'Autoradio', 'slug' => 'autoradio'],
                    ['name' => 'Haut-parleurs', 'slug' => 'haut-parleurs'],
                    ['name' => 'Supports & Chargeurs', 'slug' => 'supports-chargeurs'],
                ]
            ],
            [
                'name' => 'Confort',
                'slug' => 'confort',
                'sort_order' => 5,
                'children' => [
                    ['name' => 'Coussins & Appuie-tête', 'slug' => 'coussins-appuie-tete'],
                    ['name' => 'Organiseurs', 'slug' => 'organiseurs'],
                    ['name' => 'Accessoires Voyage', 'slug' => 'accessoires-voyage'],
                ]
            ],
            [
                'name' => 'Sécurité',
                'slug' => 'securite',
                'sort_order' => 6,
                'children' => [
                    ['name' => 'Caméras de Recul', 'slug' => 'cameras-recul'],
                    ['name' => 'Alarmes & Antivol', 'slug' => 'alarmes-antivol'],
                    ['name' => 'Dashcam', 'slug' => 'dashcam'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create($categoryData);

            foreach ($children as $childData) {
                Category::create([
                    ...$childData,
                    'parent_id' => $category->id,
                    'sort_order' => 0,
                ]);
            }
        }
    }
}