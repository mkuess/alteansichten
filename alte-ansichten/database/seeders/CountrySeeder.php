<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::updateOrCreate(
            ['slug' => 'austria'],
            [
                'name'     => 'Austria',
                'iso_code' => 'AT',
                'status'   => 'published',
            ]
        );
    }
}
