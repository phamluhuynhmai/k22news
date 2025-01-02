<?php

namespace App\Repositories;

use App\Models\Gallery;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class GalleryRepository
 * Repository xử lý các thao tác liên quan đến Gallery (Thư viện ảnh)
 */
class GalleryRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm
     * @var array
     */
    public $fieldSearchable = [
        'title',
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
     * Xác định model được sử dụng cho repository này
     * @return string
     */
    public function model()
    {
        return Gallery::class;
    }

    /**
     * Lưu trữ gallery mới
     * @param array $input Dữ liệu đầu vào
     * @return bool
     * @throws UnprocessableEntityHttpException
     */
    public function store($input)
    {
        try {
            // Bắt đầu transaction
            DB::beginTransaction();

            // Tạo gallery mới
            $gallery = Gallery::create($input);

            // Xử lý upload nhiều ảnh nếu có
            if (isset($input['images']) && ! empty($input['images'])) {
                foreach ($input['images'] as $image) {
                    // Thêm từng ảnh vào media collection
                    $gallery->addMedia($image)->toMediaCollection(Gallery::GALLERY_IMAGE, config('app.media_disc'));
                }
            }
            // Lưu các thay đổi
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin gallery
     * @param array $input Dữ liệu cập nhật
     * @param int $id ID của gallery
     * @return bool
     * @throws UnprocessableEntityHttpException
     */
    public function updateGallery($input, $id)
    {
        try {
            // Bắt đầu transaction
            DB::beginTransaction();
            
            // Tìm gallery cần cập nhật
            $gallery = Gallery::whereId($id)->firstorFail();
            
            // Cập nhật thông tin
            $gallery->update($input);

            // Xử lý cập nhật ảnh nếu có
            if (isset($input['images']) && ! empty($input['images'])) {
                // Xóa tất cả ảnh cũ
                $gallery->clearMediaCollection(Gallery::GALLERY_IMAGE);
                
                // Thêm các ảnh mới
                foreach ($input['images'] as $image) {
                    $gallery->addMedia($image)->toMediaCollection(Gallery::GALLERY_IMAGE,
                        config('app.media_disc'));
                }
            }
            // Lưu các thay đổi
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
