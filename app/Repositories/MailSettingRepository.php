<?php

namespace App\Repositories;

use App\Models\MailSetting;

/**
 * Repository class để xử lý các thao tác liên quan đến cài đặt email
 */
class MailSettingRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm
     * @var array
     */
    public $fieldSearchable = [

    ];

    /**
     * Lấy danh sách các trường có thể tìm kiếm
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Xác định model được sử dụng cho repository này
     * @return string
     */
    public function model()
    {
        return MailSetting::class;
    }

    /**
     * Cập nhật cài đặt email
     * @param array $input Dữ liệu đầu vào cần cập nhật
     * @param int $id ID của bản ghi cần cập nhật
     */
    public function update($input, $id)
    {
        // Tìm bản ghi cài đặt email theo ID
        $mailsetting = MailSetting::find($id);

        // Xử lý cài đặt xác thực email
        if (isset($input['email_setting'])) {
            $input['email_verification'] = (isset($input['email_verification'])) 
                ? MailSetting::EMAIL_VERIFICATION_ACTIVE 
                : MailSetting::EMAIL_VERIFICATION_DEACTIVE;
        }

        // Xử lý cài đặt tin nhắn liên hệ
        if (isset($input['contact_setting'])) {
            $input['contact_messages'] = (isset($input['contact_messages'])) 
                ? MailSetting::CONTACT_MESSAGES_ACTIVE 
                : MailSetting::CONTACT_MESSAGES_DEACTIVE;

            $mailsetting['contact_mail'] = $input['contact_mail'];
        }

        // Cập nhật các thay đổi vào database
        $mailsetting->update($input);
    }
}
