<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\State;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $kaernten = State::where('slug', 'kaernten')->firstOrFail();
        $wien     = State::where('slug', 'wien')->firstOrFail();

        $districts = [
            // Kärnten
            ['state' => $kaernten, 'name' => 'Klagenfurt Stadt',     'slug' => 'klagenfurt-stadt',      'code' => 'KL'],
            ['state' => $kaernten, 'name' => 'Klagenfurt Land',      'slug' => 'klagenfurt-land',       'code' => 'KL-L'],
            ['state' => $kaernten, 'name' => 'Villach Stadt',        'slug' => 'villach-stadt',         'code' => 'VI'],
            ['state' => $kaernten, 'name' => 'Villach Land',         'slug' => 'villach-land',          'code' => 'VI-L'],
            ['state' => $kaernten, 'name' => 'Sankt Veit an der Glan','slug' => 'sankt-veit-an-der-glan','code' => 'SV'],
            ['state' => $kaernten, 'name' => 'Feldkirchen',          'slug' => 'feldkirchen',           'code' => 'FK'],
            ['state' => $kaernten, 'name' => 'Völkermarkt',          'slug' => 'voelkermarkt',          'code' => 'VK'],
            ['state' => $kaernten, 'name' => 'Wolfsberg',            'slug' => 'wolfsberg',             'code' => 'WO'],
            ['state' => $kaernten, 'name' => 'Spittal an der Drau',  'slug' => 'spittal-an-der-drau',   'code' => 'SP'],
            ['state' => $kaernten, 'name' => 'Hermagor',             'slug' => 'hermagor',              'code' => 'HE'],
            // Wien
            ['state' => $wien,     'name' => 'Wien',                 'slug' => 'wien',                  'code' => 'W'],
        ];

        foreach ($districts as $data) {
            District::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'state_id' => $data['state']->id,
                    'name'     => $data['name'],
                    'code'     => $data['code'],
                    'status'   => 'published',
                ]
            );
        }
    }
}
