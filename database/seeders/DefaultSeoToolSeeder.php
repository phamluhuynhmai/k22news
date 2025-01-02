<?php

namespace Database\Seeders;

use App\Models\SeoTool;
use Illuminate\Database\Seeder;

class DefaultSeoToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seoTools =
            [
                'lang_id' => '6',
                'site_title' => 'K22 News',
                'home_title' => 'Trang chur',
                'site_description' => '',
                'keyword' => '',
                'google_analytics' => '',
            ];
        SeoTool::create($seoTools);
    }
}
