<?php

namespace Database\Seeders;

use App\Models\MailSetting;
use Illuminate\Database\Seeder;

/**
 * Class DefaultMailSettingSeeder
 * Seeder này dùng để tạo cấu hình email mặc định cho hệ thống
 */
class DefaultMailSettingSeeder extends Seeder
{
    /**
     * Chạy database seeds.
     * Phương thức này sẽ tạo một bản ghi cấu hình email mặc định
     *
     * @return void
     */
    public function run()
    {
        MailSetting::create([
            // Kiểu mã hóa email (TLS)
            'encryption' => MailSetting::TLS,
            
            // Thư viện gửi mail được sử dụng
            'mail_library' => MailSetting::SWIFT_MAILER,
            
            // Giao thức gửi mail (SMTP)
            'mail_protocol' => MailSetting::SMTP,
            
            // Địa chỉ máy chủ email
            'mail_host' => 'mail@codingest.com',
            
            // Cổng kết nối email
            'mail_port' => 587,
            
            // Tên đăng nhập email
            'mail_username' => 'info@codingest.com',
            
            // Mật khẩu email
            'mail_password' => 'mail@123',
            
            // Tiêu đề hiển thị trong email
            'mail_title' => 'Varient',
            
            // Địa chỉ email nhận phản hồi
            'reply_to' => 'info2@codingest.com',
            
            // Bật/tắt xác thực email (1: bật, 0: tắt)
            'email_verification' => 1,
            
            // Bật/tắt tin nhắn liên hệ (1: bật, 0: tắt)
            'contact_messages' => 1,
            
            // Email nhận tin nhắn liên hệ
            'contact_mail' => 'info3@codingest.com',
            
            // Email gửi đi
            'send_mail' => 'info4@codingest.com',
        ]);
    }
}
