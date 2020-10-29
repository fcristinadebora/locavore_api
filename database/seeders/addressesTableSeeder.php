<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class addressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('addresses')->seed(
            [
                'street' => 'BR 283',
                'number' => 2638,
                'district' => 'Bairro dos Estados',
                'city' => 'Concórdia',
                'state' => 'Santa Catarina',
                'country' => 'Brasil',
                'complement' => 'Primeiro andar',
                'lat' => -27.217899,
                'long' => -52.047539,
                'name' => 'Ponto de Venda',
                'postal_code' => '89700-000',
                'user_id' => 1,
            ],
            [
                'street' => 'Rua A',
                'number' => 288,
                'district' => 'Poente do Sol',
                'city' => 'Concórdia',
                'state' => 'Santa Catarina',
                'country' => 'Brasil',
                'complement' => null,
                'lat' => -27.221775,
                'long' => -52.061246,
                'name' => 'Matriz',
                'postal_code' => '89700-000',
                'user_id' => 2,
            ],
            [
                'street' => 'Rua Rio de Janeiro',
                'number' => 266,
                'district' => 'Bairro dos Estados',
                'city' => 'Concórdia',
                'state' => 'Santa Catarina',
                'country' => 'Brasil',
                'complement' => null,
                'lat' => -27.211932,
                'long' => -52.047496,
                'name' => 'Fazenda',
                'postal_code' => '89700-000',
                'user_id' => 3,
            ],
            [
                'street' => 'Rua Anitta Garibaldi',
                'number' => 3100,
                'district' => 'Bairro Primavera',
                'city' => 'Concórdia',
                'state' => 'Santa Catarina',
                'country' => 'Brasil',
                'complement' => null,
                'lat' => -27.205231,
                'long' => -52.020296,
                'name' => 'Fazenda',
                'postal_code' => '89700-000',
                'user_id' => 4,
            ],
            [
                'street' => 'Rua Ângelo Scandolara',
                'number' => 89711-162,
                'district' => 'Bairro Imigrantes',
                'city' => 'Concórdia',
                'state' => 'Santa Catarina',
                'country' => 'Brasil',
                'complement' => null,
                'lat' => -27.238431,
                'long' => -52.004213,
                'name' => 'Endereço principal',
                'postal_code' => '89711-162',
                'user_id' => 5,
            ],
            [
                'street' => 'Rua Ângelo Scandolara',
                'number' => 89711-162,
                'district' => 'Bairro Imigrantes',
                'city' => 'Concórdia',
                'state' => 'Santa Catarina',
                'country' => 'Brasil',
                'complement' => null,
                'lat' => -27.238431,
                'long' => -52.004213,
                'name' => 'Endereço principal',
                'postal_code' => '89711-162',
                'user_id' => 5,
            ],
            [
                'street' => 'Rua Ângelo Scandolara',
                'number' => 89711-162,
                'district' => 'Bairro Imigrantes',
                'city' => 'Concórdia',
                'state' => 'Santa Catarina',
                'country' => 'Brasil',
                'complement' => null,
                'lat' => -27.238431,
                'long' => -52.004213,
                'name' => 'Endereço principal',
                'postal_code' => '89711-162',
                'user_id' => 5,
            ]
        );
    }
}
