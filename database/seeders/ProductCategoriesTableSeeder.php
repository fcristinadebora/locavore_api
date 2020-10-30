<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_categories')->insert([
            ['description' => 'Orgânicos'],
            ['description' => 'Naturais'],
            ['description' => 'Artesanais'],
            ['description' => 'Veganos (sem origem animal)'],
            ['description' => 'Vegetariano (sem carne)'],
            ['description' => 'Colonial'],
            ['description' => 'Outros']
        ]);
    }
}
