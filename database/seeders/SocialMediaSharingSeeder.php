<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SocialMediaSharingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create(['key' => 'facebook', 'value' => '1']);
        Setting::create(['key' => 'twitter', 'value' => '1']);
        Setting::create(['key' => 'linkedin', 'value' => '1']);
        Setting::create(['key' => 'reddit', 'value' => '1']);
        Setting::create(['key' => 'whatsapp', 'value' => '1']);
    }
}
