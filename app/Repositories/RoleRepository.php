<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Models\Role;

/**
 * Class RoleRepository
 *
 * @version 
 * Repository xử lý các thao tác liên quan đến vai trò (Role)

 */
class RoleRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm
     * @var array
     */
    protected $fieldSearchable = [
        'name',
    ];

    /**
     * Trả về danh sách các trường có thể tìm kiếm
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Cấu hình Model được sử dụng cho Repository
     */
    public function model()
    {
        return Role::class;
    }

    /**
     * Lấy danh sách tất cả các quyền
     * @return mixed Collection các Permission
     */
    public function getPermissions()
    {
        $permissions = Permission::toBase()->get();

        return $permissions;
    }

    /**
     * Tạo mới một vai trò
     * @param array $input Dữ liệu đầu vào
     * @return Role
     */
    public function store($input)
    {
        // Chuyển display_name thành chữ thường
        $displayName = strtolower($input['display_name']);
        // Thay thế khoảng trắng bằng dấu gạch dưới cho tên
        $input['name'] = str_replace(' ', '_', $displayName);

        /** @var Role $role */
        $role = Role::create($input);

        // Nếu có permission_id được truyền vào, đồng bộ các quyền cho vai trò
        if (isset($input['permission_id']) && ! empty($input['permission_id'])) {
            $role->permissions()->sync($input['permission_id']);
        }

        return $role;
    }

    /**
     * Cập nhật thông tin vai trò
     * @param  array  $input Dữ liệu cập nhật
     * @param  int  $id ID của vai trò
     * @return Role
     */
    public function update($input, $id): Role
    {
        // Xử lý tên hiển thị và tên vai trò
        $displayName = strtolower($input['display_name']);
        $str = str_replace(' ', '_', $displayName);

        // Tìm vai trò theo ID
        $role = Role::findById($id);
        
        /** @var Role $role */
        $role->update([
            'name' => $str,
            'display_name' => $input['display_name'],
        ]);

        // Cập nhật các quyền của vai trò nếu có
        if (isset($input['permission_id']) && ! empty($input['permission_id'])) {
            $role->permissions()->sync($input['permission_id']);
        }

        return $role;
    }
}
