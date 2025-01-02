<?php

namespace App\Repositories;

use App\Models\Page;

/**
 * Class PageRepository
 * Repository xử lý các thao tác liên quan đến model Page
 */
class PageRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm
     */
    public $fieldSearchable = [
        'name',
        'title',
        'meta_title',
        'lang_id',
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
        return Page::class;
    }

    /**
     * Lưu trữ một trang mới
     * 
     * @param array $input Dữ liệu đầu vào
     * @return void
     */
    public function store($input)
    {
        // Thiết lập trạng thái hiển thị của trang
        $input['visibility'] = (isset($input['visibility'])) ? Page::VISIBILITY_ACTIVE : Page::VISIBILITY_DEACTIVE;

        // Thiết lập hiển thị breadcrumb
        $input['show_breadcrumb'] = (isset($input['show_breadcrumb'])) ? Page::SHOW_BREADCRUMP_ACTIVE : Page::SHOW_BREADCRUMP_DEACTIVE;

        // Thiết lập hiển thị cột bên phải
        $input['show_right_column'] = (isset($input['show_right_column'])) ? Page::SHOW_RIGHT_ACTIVE : Page::SHOW_RIGHT_DEACTIVE;

        // Thiết lập quyền truy cập
        $input['permission'] = (isset($input['permission'])) ? Page::PERMISION_ACTIVE : Page::PERMISION_DEACTIVE;

        // Thiết lập hiển thị tiêu đề
        $input['show_title'] = (isset($input['show_title'])) ? Page::SHOW_TITLE_ACTIVE : Page::SHOW_TITLE_DEACTIVE;

        Page::create($input);
    }

    /**
     * Cập nhật thông tin của một trang
     * 
     * @param array $input Dữ liệu cập nhật
     * @param int $id ID của trang cần cập nhật
     * @return void
     */
    public function update($input, $id)
    {
        $page = Page::find($id);

        // Thiết lập trạng thái hiển thị của trang
        $input['visibility'] = (isset($input['visibility'])) ? Page::VISIBILITY_ACTIVE : Page::VISIBILITY_DEACTIVE;

        // Thiết lập hiển thị breadcrumb
        $input['show_breadcrumb'] = (isset($input['show_breadcrumb'])) ? Page::SHOW_BREADCRUMP_ACTIVE : Page::SHOW_BREADCRUMP_DEACTIVE;

        // Thiết lập hiển thị cột bên phải
        $input['show_right_column'] = (isset($input['show_right_column'])) ? Page::SHOW_RIGHT_ACTIVE : Page::SHOW_RIGHT_DEACTIVE;

        // Thiết lập quyền truy cập
        $input['permission'] = (isset($input['permission'])) ? Page::PERMISION_ACTIVE : Page::PERMISION_DEACTIVE;

        // Thiết lập hiển thị tiêu đề
        $input['show_title'] = (isset($input['show_title'])) ? Page::SHOW_TITLE_ACTIVE : Page::SHOW_TITLE_DEACTIVE;

        $page->update($input);
    }
}
