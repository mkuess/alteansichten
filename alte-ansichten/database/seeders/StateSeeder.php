<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $austria = Country::where('iso_code', 'AT')->firstOrFail();

        $states = [
            ['name' => 'Burgenland',       'slug' => 'burgenland',       'code' => 'AT-1'],
            ['name' => 'Kärnten',          'slug' => 'kaernten',         'code' => 'AT-2'],
            ['name' => 'Niederösterreich', 'slug' => 'niederoesterreich','code' => 'AT-3'],
            ['name' => 'Oberösterreich',   'slug' => 'oberoesterreich',  'code' => 'AT-4'],
            ['name' => 'Salzburg',         'slug' => 'salzburg',         'code' => 'AT-5'],
            ['name' => 'Steiermark',       'slug' => 'steiermark',       'code' => 'AT-6'],
            ['name' => 'Tirol',            'slug' => 'tirol',            'code' => 'AT-7'],
            ['name' => 'Vorarlberg',       'slug' => 'vorarlberg',       'code' => 'AT-8'],
            ['name' => 'Wien',             'slug' => 'wien',             'code' => 'AT-9'],
        ];

        foreach ($states as $data) {
            State::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'country_id' => $austria->id,
                    'name'       => $data['name'],
                    'code'       => $data['code'],
                    'status'     => 'published',
                ]
            );
        }
    }
}
