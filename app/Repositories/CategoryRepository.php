<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Navigation;

/**
 * Repository xử lý logic nghiệp vụ cho Category
 */
class CategoryRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm
     */
    public $fieldSearchable = [
        'name',
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
        return Category::class;
    }

    /**
     * Tạo mới một category
     * @param  array  $input Dữ liệu đầu vào
     * @return bool
     */
    public function create($input)
    {
        // Xử lý trạng thái hiển thị trong menu
        $input['show_in_menu'] = (isset($input['show_in_menu'])) ? Category::SHOW_MENU_ACTIVE : Category::SHOW_MENU_DEACTIVE;
        // Xử lý trạng thái hiển thị ở trang chủ
        $input['show_in_home_page'] = (isset($input['show_in_home_page'])) ? Category::SHOW_MENU_HOME_ACTIVE : Category::SHOW_MENU_HOME_DEACTIVE;

        // Tạo category mới
        $category = Category::create($input);

        // Tính toán thứ tự navigation mới
        $navigationOrder = Navigation::whereNull('parent_id')->count() + 1;
        // Tạo navigation cho category
        Navigation::create([
            'navigationable_type' => Category::class,
            'navigationable_id' => $category['id'],
            'order_id' => $navigationOrder,
        ]);

        return true;
    }

    /**
     * Cập nhật thông tin category
     * @param array $input Dữ liệu cập nhật
     * @param int $id ID của category
     * @return bool
     */
    public function update($input, $id)
    {
        // Tìm category cần cập nhật
        $category = Category::findOrFail($id);
        // Cập nhật trạng thái hiển thị
        $input['show_in_menu'] = isset($input['show_in_menu']);
        $input['show_in_home_page'] = isset($input['show_in_home_page']);
        // Thực hiện cập nhật
        $category->update($input);

        return true;
    }
}
