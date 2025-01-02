<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Chạy database seeds.
     * Phương thức này sẽ tạo các cài đặt mặc định cho hệ thống
     * @return void
     */
    public function run()
    {
        // Định nghĩa đường dẫn mặc định cho logo và favicon
        $logoUrl = '/assets/image/k22-logo.png';
        $favicon = '/assets/image/favicon-k22.png';

        // Cài đặt thông tin cơ bản của ứng dụng
        Setting::create(['key' => 'application_name', 'value' => 'K22 News']);
        Setting::create(['key' => 'contact_no', 'value' => '+84963656729']);
        Setting::create(['key' => 'email', 'value' => 'phamluhuynhmai@gmail.com']);
        Setting::create(['key' => 'copy_right_text', 'value' => 'All Rights Reserved ©2024']);

        // Cài đặt Captcha
        Setting::create(['key' => 'site_key', 'value' => ' ']); // Khóa site cho captcha
        Setting::create(['key' => 'secret_key', 'value' => ' ']); // Khóa bí mật cho captcha
        Setting::create(['key' => 'show_captcha', 'value' => 0]); // Bật/tắt hiển thị captcha

        // Cài đặt đường dẫn mạng xã hội
        Setting::create(['key' => 'facebook_url', 'value' => 'https://www.facebook.com/phamluhuynhmai/']); // Facebook
        Setting::create(['key' => 'twitter_url', 'value' => 'https://twitter.com/jonalisamooi']); // Twitter
        Setting::create(['key' => 'instagram_url', 'value' => 'https://www.instagram.com/phamluhuynhmai']); // Instagram
        Setting::create(['key' => 'pinterest_url', 'value' => 'https://www.pinterest.com/jonalisamooi']); // Pinterest
        Setting::create(['key' => 'linkedin_url', 'value' => 'https://www.linkedin.com/']); // LinkedIn
        Setting::create(['key' => 'vk_url', 'value' => 'https://vk.com/?lang=en']); // VK
        Setting::create(['key' => 'telegram_url', 'value' => 'https://www.telegram.org/']); // Telegram
        Setting::create(['key' => 'youtube_url', 'value' => 'https://www.youtube.com/@nvdong15']); // YouTube

        // Cài đặt cookie
        Setting::create(['key' => 'show_cookie', 'value' => 1]); // Bật/tắt thông báo cookie
        Setting::create(['key' => 'cookie_warning', 'value' => 'Muốn lướt thì cho phép bật cookie đi đừng chờ chi nữa nhe.']); // Nội dung cảnh báo cookie

        // Cài đặt hình ảnh
        Setting::create(['key' => 'logo', 'value' => $logoUrl]); // Logo trang web
        Setting::create(['key' => 'favicon', 'value' => $favicon]); // Favicon trang web

        // Cài đặt thông tin liên hệ và giới thiệu
        Setting::create(['key' => 'contact_address', 'value' => '280 Đường An Dương Vương, Phường 4, Quận 5, Thành Phố Hồ Chí Minh, Việt Nam.']);
        Setting::create(['key' => 'about_text', 'value' => "Tòa soạn K22 với phương châm nước tới mép tai mới nhảy, đề tài cuối kỳ cuối cùng cũng hoàn thành. Tuy nhiên đã để lại trên người của những nhà sáng lập toà soạn K22 nhiều di chứng như mắt thâm, mặt mụn, tóc rụng, lưng đau,... Do đó tòa soạn vô cùng mong mỏi được thầy cho đề tài này 10 điểm ạ."]);

        // Các trang thông tin
        Setting::create(['key' => 'terms&conditions', 'value' => '']); // Điều khoản và điều kiện
        Setting::create(['key' => 'privacy', 'value' => '']); // Chính sách bảo mật
        Setting::create(['key' => 'support', 'value' => '']); // Trang hỗ trợ

        // Cài đặt khác
        Setting::create(['key' => 'comment_approved', 'value' => '1']); // Tự động phê duyệt bình luận
        Setting::create(['key' => 'front_language', 'value' => '6']); // Ngôn ngữ mặc định cho giao diện người dùng
    }
}
