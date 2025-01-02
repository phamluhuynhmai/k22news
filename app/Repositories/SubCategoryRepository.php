<?php

namespace App\Repositories;

use App\Models\Navigation;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class SubCategoryRepository
 * Repository xử lý logic nghiệp vụ cho danh mục con
 */
class SubCategoryRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm
     */
    public $fieldSearchable = [
        'name',
    ];

    /**
     * Trả về danh sách các trường có thể tìm kiếm
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Xác định model được sử dụng cho repository này
     */
    public function model()
    {
        return SubCategory::class;
    }

    /**
     * Tạo mới một danh mục con
     * @param array $input Dữ liệu đầu vào
     * @return bool
     */
    public function create($input)
    {
        // Xử lý trạng thái hiển thị trong menu
        $input['show_in_menu'] = (isset($input['show_in_menu'])) ? SubCategory::SHOW_MENU_ACTIVE : SubCategory::SHOW_MENU_DEACTIVE;
        $subCategory = SubCategory::create($input);

        // Tính toán thứ tự navigation cho danh mục con mới
        $navigationOrder = Navigation::whereNavigationableType(SubCategory::class)
                ->whereParentId($subCategory['parent_category_id'])->count() + 1;

        // Tạo bản ghi navigation cho danh mục con
        Navigation::create([
            'navigationable_type' => SubCategory::class,
            'navigationable_id' => $subCategory['id'],
            'order_id' => $navigationOrder,
            'parent_id' => $subCategory['parent_category_id'] ?? null,
        ]);

        return true;
    }

    /**
     * Cập nhật thông tin danh mục con
     * @param array $input Dữ liệu cập nhật
     * @param SubCategory $subCategory Đối tượng danh mục con cần cập nhật
     * @return bool
     */
    public function update($input, $subCategory)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem có thay đổi danh mục cha không
            $oldParentId = $subCategory->parent_category_id;
            $changeParent = $input['parent_category_id'] != $oldParentId;
            $input['show_in_menu'] = isset($input['show_in_menu']);

            $subCategory->update($input);

            if ($changeParent) {
                // Cập nhật thứ tự navigation cho danh mục mới
                $navigationOrder = Navigation::whereNavigationableType(SubCategory::class)
                        ->whereParentId($subCategory->parent_category_id)->count() + 1;
                $subCategory->navigation->update([
                    'order_id' => $navigationOrder,
                    'parent_id' => $subCategory->parent_category_id,
                ]);

                // Sắp xếp lại thứ tự các navigation trong danh mục cũ
                $subsNavigation = Navigation::whereNavigationableType(SubCategory::class)
                        ->whereParentId($oldParentId)->orderBy('order_id')->get();
                foreach ($subsNavigation as $key => $navigation) {
                    $navigation->update([
                        'order_id' => $key + 1,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }
}
