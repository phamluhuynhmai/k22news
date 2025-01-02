<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

/**
 * Class DefaultLanguageSeeder
 * Seeder này dùng để tạo dữ liệu mặc định cho bảng languages
 */
class DefaultLanguageSeeder extends Seeder
{
    /**
     * Chạy seeder để tạo dữ liệu.
     * 
     * Hàm này sẽ tạo các bản ghi ngôn ngữ mặc định trong hệ thống
     * bao gồm: Tiếng Anh, Tiếng Trung, Tiếng Tây Ban Nha,
     * Tiếng Đức và Tiếng Việt
     *
     * @return void
     */
    public function run()
    {
        // Mảng chứa thông tin các ngôn ngữ mặc định
        $languages = [
            [
                'name' => 'English', // Tiếng Anh
                'iso_code' => 'en',  // Mã ISO của ngôn ngữ
                'is_default' => false, // Không phải ngôn ngữ mặc định
            ],
            
            [
                'name' => 'Chinese', // Tiếng Trung
                'iso_code' => 'zh',
                'is_default' => false,
            ],
            [
                'name' => 'Spanish', // Tiếng Tây Ban Nha
                'iso_code' => 'es',
                'is_default' => false,
            ],
            [
                'name' => 'German', // Tiếng Đức
                'iso_code' => 'de',
                'is_default' => false,
            ],
            [
                'name' => 'Vietnamese', // Tiếng Việt
                'iso_code' => 'vi',
                'is_default' => true, // Đặt làm ngôn ngữ mặc định
            ],
        ];

        // Duyệt qua mảng và tạo bản ghi cho từng ngôn ngữ
        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
