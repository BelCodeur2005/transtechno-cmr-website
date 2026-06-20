<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Contact
            ['key' => 'phone_1',     'value' => '+237 674 64 53 54', 'group' => 'contact'],
            ['key' => 'phone_2',     'value' => '+237 699 67 40 36', 'group' => 'contact'],
            ['key' => 'phone_3',     'value' => '+237 233 37 33 62', 'group' => 'contact'],
            ['key' => 'whatsapp',    'value' => '237699674036',       'group' => 'contact'],
            ['key' => 'email',       'value' => 'office@transtechno-cmr.com', 'group' => 'contact'],

            // Adresse
            ['key' => 'address_line1', 'value' => 'Village carrefour, entrée bille', 'group' => 'address'],
            ['key' => 'address_line2', 'value' => 'Immeuble Pharmacie Sainte-Marie, 2ème niveau', 'group' => 'address'],
            ['key' => 'address_city',  'value' => 'Douala, Cameroun', 'group' => 'address'],

            // Réseaux sociaux
            ['key' => 'facebook_url',  'value' => 'https://www.facebook.com/TranstechnoCameroun', 'group' => 'social'],
            ['key' => 'linkedin_url',  'value' => '', 'group' => 'social'],

            // Légal
            ['key' => 'company_name',  'value' => 'Transtechno Cameroun', 'group' => 'legal'],
            ['key' => 'rccm',          'value' => 'RC/BFM/B/586',         'group' => 'legal'],
            ['key' => 'niu',           'value' => 'M111914366866R',        'group' => 'legal'],
            ['key' => 'rib',           'value' => '10033-05203-03016000260-31', 'group' => 'legal'],
            ['key' => 'cnps',          'value' => '380-0113880-000-E',     'group' => 'legal'],
            ['key' => 'host_name',     'value' => 'PlanetHoster Inc.', 'group' => 'legal'],
            ['key' => 'host_address',  'value' => '4416 Louis-B.-Mayer, Laval, Québec, H7P 0G1, Canada', 'group' => 'legal'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], [
                'value' => $setting['value'],
                'group' => $setting['group'],
            ]);
        }
    }
}
