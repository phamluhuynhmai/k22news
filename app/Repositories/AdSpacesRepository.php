<?php

namespace App\Repositories;

use App\Models\AdSpaces;

/**
 * Class AdSpacesRepository
 * Repository class để xử lý logic nghiệp vụ liên quan đến quảng cáo
 */
class AdSpacesRepository extends BaseRepository
{
    /**
     * Danh sách các trường có thể tìm kiếm
     * @var array
     */
    protected $fieldSearchable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password',
        'gender',
        'role',
    ];

    /**
     * Trả về danh sách các trường có thể tìm kiếm
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Cấu hình Model được sử dụng trong Repository
     * @return string
     */
    public function model(): string
    {
        return AdSpaces::class;
    }

    /**
     * Lưu trữ thông tin quảng cáo
     * @param array $input Dữ liệu đầu vào từ form
     * @return bool
     */
    public function store($input): bool
    {
        // Lấy danh sách quảng cáo dựa trên ad-space được chọn
        $data = AdSpaces::whereAdSpaces($input['ad-space'])->get();

        // Cập nhật từng quảng cáo
        foreach ($data as $key => $value) {
            // Cập nhật URL và mã quảng cáo
            $value->update([
                'ad_url' => $input['ad-url'][$key],
                'code' => $input['ad-code'][$key],
            ]);

            // Xử lý upload banner quảng cáo nếu có
            if (! empty($input['ad_banner'][$key])) {
                // Xóa ảnh cũ trước khi thêm ảnh mới
                $value->clearMediaCollection(AdSpaces::IMAGE_POST);
                // Upload ảnh mới vào thư mục media được cấu hình
                $value->addMedia($input['ad_banner'][$key])->toMediaCollection(AdSpaces::IMAGE_POST, config('app.media_disc'));
            }
        }

        return true;
    }
}
