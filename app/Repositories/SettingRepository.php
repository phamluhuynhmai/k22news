<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;

/**
 * Class SettingRepository - Quản lý các cài đặt hệ thống
 */
class SettingRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm
     * @var array
     */
    public $fieldSearchable = [
        'application_name',
    ];

    /**
     * Trả về danh sách các trường có thể tìm kiếm
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Xác định model được sử dụng cho repository
     * @return string
     */
    public function model()
    {
        return Setting::class;
    }

    /**
     * Cập nhật các cài đặt hệ thống
     * @param array $input Dữ liệu đầu vào cần cập nhật
     * @param int $userId ID của người dùng thực hiện cập nhật
     * @return void
     */
    public function update($input, $userId)
    {
        // Loại bỏ token khỏi dữ liệu đầu vào
        $inputArr = Arr::except($input, ['_token']);

        // Xử lý cài đặt chung
        if ($inputArr['sectionName'] == 'general') {
            $inputArr['application_name'] = (empty($inputArr['app_name'])) ? '' : $inputArr['app_name'];
            $inputArr['contact_no'] = (empty($inputArr['contact_no'])) ? '' : $inputArr['contact_no'];
            $inputArr['email'] = (empty($inputArr['email'])) ? '' : $inputArr['email'];
        }

        // Xử lý cài đặt captcha
        if ($inputArr['sectionName'] == 'general_1') {
            $inputArr['show_captcha'] = (empty($inputArr['show_captcha'])) ? '0' : '1';
            $inputArr['show_captcha_on_registration'] = (empty($inputArr['show_captcha_on_registration'])) ? '0' : '1';
            $inputArr['site_key'] = (empty($inputArr['site_key'])) ? '' : $inputArr['site_key'];
            $inputArr['secret_key'] = (empty($inputArr['secret_key'])) ? '' : $inputArr['secret_key'];
        }

        // Xử lý cài đặt mạng xã hội
        if ($inputArr['sectionName'] == 'general_2') {
            $inputArr['whatsapp'] = (empty($inputArr['whatsapp'])) ? '0' : '1';
            $inputArr['linkedin'] = (empty($inputArr['linkedin'])) ? '0' : '1';
            $inputArr['twitter'] = (empty($inputArr['twitter'])) ? '0' : '1';
            $inputArr['facebook'] = (empty($inputArr['facebook'])) ? '0' : '1';
            $inputArr['reddit'] = (empty($inputArr['reddit'])) ? '0' : '1';
        }

        // Xử lý thông tin liên hệ
        if ($inputArr['sectionName'] == 'contact_information') {
            $inputArr['contact_address'] = (empty($inputArr['contact_address'])) ? '' : $inputArr['contact_address'];
            $inputArr['about_text'] = (empty($inputArr['about_text'])) ? '' : $inputArr['about_text'];
        }

        // Xử lý cảnh báo cookie
        if ($inputArr['sectionName'] == 'cookie_warning') {
            $inputArr['cookie_warning'] = (!empty($inputArr['cookie_warning'])) ? $inputArr['cookie_warning'] :
                'Your experience on this site will be improved by allowing cookies.';
        }

        // Xử lý cài đặt quảng cáo
        if ($inputArr['sectionName'] == 'ad_management') {
            // Cài đặt vị trí hiển thị quảng cáo
            $inputArr['header'] = (empty($inputArr['header'])) ? '0' : '1';
            $inputArr['index_top'] = (empty($inputArr['index_top'])) ? '0' : '1';
            $inputArr['index_bottom'] = (empty($inputArr['index_bottom'])) ? '0' : '1';
            $inputArr['post_details'] = (empty($inputArr['post_details'])) ? '0' : '1';
            $inputArr['details_side'] = (empty($inputArr['details_side'])) ? '0' : '1';
            $inputArr['categories'] = (empty($inputArr['categories'])) ? '0' : '1';
            $inputArr['gallery'] = (empty($inputArr['gallery'])) ? '0' : '1';
            
            // Cài đặt hiển thị các bài viết đặc biệt
            $inputArr['trending_post'] = (empty($inputArr['trending_post'])) ? '0' : '1';
            $inputArr['popular_news'] = (empty($inputArr['popular_news'])) ? '0' : '1';
            $inputArr['trending_post_index_page'] = (empty($inputArr['trending_post_index_page'])) ? '0' : '1';
            $inputArr['popular_news_index_page'] = (empty($inputArr['popular_news_index_page'])) ? '0' : '1';
            $inputArr['recommended_post_index_page'] = (empty($inputArr['recommended_post_index_page'])) ? '0' : '1';
        }

        // Tạo sitemap
        if ($inputArr['sectionName'] == 'generate_sitemap') {
            Artisan::call('generate:sitemap');
        }

        // Xử lý cài đặt nâng cao
        if($inputArr['sectionName'] == 'advanced_setting'){
            $inputArr['emoji_system'] = (empty($inputArr['emoji_system'])) ? '0' : '1';
            $inputArr['registration_system'] = (empty($inputArr['registration_system'])) ? '0' : '1';
        }

        // Cập nhật từng cài đặt vào database
        foreach ($inputArr as $key => $value) {
            /** @var Setting $setting */
            $setting = Setting::where('key', $key)->first();
            if (!$setting) {
                continue;
            }

            $setting->update(['value' => $value]);

            // Xử lý upload logo
            if (in_array($key, ['logo']) && !empty($value)) {
                $setting->clearMediaCollection(Setting::LOGO);
                $media = $setting->addMedia($value)->toMediaCollection(Setting::LOGO, config('app.media_disc'));
                $setting->update(['value' => $media->getUrl()]);
            }

            // Xử lý upload favicon
            if (in_array($key, ['favicon']) && !empty($value)) {
                $setting->clearMediaCollection(Setting::FAVICON);
                $media = $setting->addMedia($value)->toMediaCollection(Setting::FAVICON, config('app.media_disc'));
                $setting->update(['value' => $media->getUrl()]);
            }
        }
    }
}
