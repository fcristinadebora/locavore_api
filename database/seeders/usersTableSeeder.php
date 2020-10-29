<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class usersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([[
            'name' => 'O quintal da minha casa',
            'email' => 'quintaldaminhacasa@gmail.com',
            'facebook_id' => null,
            'profile_file_name' => 'meuquintal.jpg',
            'profile_file_path' => 'uploads/',
            'is_grower' => true,
            'description' => 'o Quintal da Minha Casa tem um propósito: facilitar o acesso aos melhores produtos orgânicos, levando mais bom humor para o dia das pessoas. em nosso quintal trabalhamos e nos divertimos, aprendemos e ensinamos, equilibramos a vida com nossas escolhas de consumo. seja bem-vindo, o Quintal da Minha Casa é seu quintal também ;)',
            'password' => Hash::make('123456')
        ],
        [
            'name' => 'Fazenda Larga',
            'email' => 'fazendalarga@gmail.com',
            'facebook_id' => null,
            'profile_file_name' => 'fazendalarga.jpg',
            'profile_file_path' => 'uploads/',
            'is_grower' => true,
            'description' => ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque vestibulum mattis nibh non finibus. In finibus vulputate nulla, nec vehicula sem euismod volutpat.',
            'password' => Hash::make('123456')
        ],
        [
            'name' => 'Sítio Quinto Rancho',
            'email' => 'quintorancho@gmail.com',
            'facebook_id' => null,
            'profile_file_name' => 'quintorancho.jpg',
            'profile_file_path' => 'uploads/',
            'is_grower' => true,
            'description' => ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque vestibulum mattis nibh non finibus. In finibus vulputate nulla, nec vehicula sem euismod volutpat.',
            'password' => Hash::make('123456')
        ],
        [
            'name' => 'Fazenda Comadre',
            'email' => 'fazendacomadre@gmail.com',
            'facebook_id' => null,
            'profile_file_name' => 'comadre.jpg',
            'profile_file_path' => 'uploads/',
            'is_grower' => true,
            'description' => ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque vestibulum mattis nibh non finibus. In finibus vulputate nulla, nec vehicula sem euismod volutpat.',
            'password' => Hash::make('123456')
        ],
        [
            'name' => 'Adega Porão do Valle',
            'email' => 'poraodovalle@gmail.com',
            'facebook_id' => null,
            'profile_file_name' => 'poraodovalle.jpg',
            'profile_file_path' => 'uploads/',
            'is_grower' => true,
            'description' => ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque vestibulum mattis nibh non finibus. In finibus vulputate nulla, nec vehicula sem euismod volutpat.',
            'password' => Hash::make('123456')
        ],
        [
            'name' => 'Sítio Familia Souza',
            'email' => 'familiasouza@gmail.com',
            'facebook_id' => null,
            'profile_file_name' => 'melancia.jpg',
            'profile_file_path' => 'uploads/',
            'is_grower' => true,
            'description' => ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque vestibulum mattis nibh non finibus. In finibus vulputate nulla, nec vehicula sem euismod volutpat.',
            'password' => Hash::make('123456')
        ]]);
    }
}
