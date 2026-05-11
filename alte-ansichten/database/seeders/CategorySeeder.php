<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gasthaus',     'slug' => 'gasthaus',     'sort_order' => 1],
            ['name' => 'Schule',       'slug' => 'schule',       'sort_order' => 2],
            ['name' => 'Bahnhof',      'slug' => 'bahnhof',      'sort_order' => 3],
            ['name' => 'Sakralbau',    'slug' => 'sakralbau',    'sort_order' => 4],
            ['name' => 'Handwerk',     'slug' => 'handwerk',     'sort_order' => 5],
            ['name' => 'Industriebau', 'slug' => 'industriebau', 'sort_order' => 6],
            ['name' => 'Denkmal',      'slug' => 'denkmal',      'sort_order' => 7],
            ['name' => 'Wohngebäude',  'slug' => 'wohngebaeude', 'sort_order' => 8],
            ['name' => 'Verwaltung',   'slug' => 'verwaltung',   'sort_order' => 9],
            ['name' => 'Verkehr',      'slug' => 'verkehr',      'sort_order' => 10],
            ['name' => 'Landschaft',   'slug' => 'landschaft',   'sort_order' => 11],
            ['name' => 'Sonstiges',    'slug' => 'sonstiges',    'sort_order' => 12],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name'       => $data['name'],
                    'sort_order' => $data['sort_order'],
                    'status'     => 'published',
                ]
            );
        }
    }
}
