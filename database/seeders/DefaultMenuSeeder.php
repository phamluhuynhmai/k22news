<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Navigation;
use Illuminate\Database\Seeder;

/**
 * Seeder này dùng để tạo dữ liệu mẫu cho menu mặc định
 */
class DefaultMenuSeeder extends Seeder
{
    /**
     * Chạy seeder để tạo dữ liệu
     *
     * @return void
     */
    public function run()
    {
        // Mảng chứa thông tin các menu mặc định
        $menus = [
            [
                'title' => 'Election', // Tiêu đề menu Bầu cử
                'link' => 'www.politicsinfo.com',
                'parent_menu_id' => null, // Menu gốc (không có menu cha)
                'order' => 2, // Thứ tự hiển thị
                'show_in_menu' => 1, // 1 = Hiển thị trong menu
            ],
            [
                'title' => 'Upcoming Sports', // Tiêu đề menu Sự kiện thể thao sắp diễn ra
                'link' => 'www.SportsDaily.com',
                'parent_menu_id' => 1, // Menu con của Election (id = 1)
                'order' => 1,
                'show_in_menu' => 1,
            ],
            [
                'title' => 'New Launches', // Tiêu đề menu Ra mắt mới
                'link' => 'www.MyGamez.com',
                'parent_menu_id' => 1, // Menu con của Election (id = 1)
                'order' => 3,
                'show_in_menu' => 0, // 0 = Ẩn khỏi menu
            ],
        ];

        // Duyệt qua từng menu để tạo dữ liệu
        foreach ($menus as $menu) {
            // Tạo bản ghi menu mới
            $menuId = Menu::create($menu);

            // Tính toán thứ tự navigation
            if (isset($menu['parent_menu_id'])) {
                // Đếm số navigation có cùng parent_id và cộng thêm 1
                $navigationOrder = Navigation::whereParentId($menu['parent_menu_id'])->count() + 1;
            } else {
                // Đếm số navigation gốc và cộng thêm 1
                $navigationOrder = Navigation::whereNull('parent_id')->count() + 1;
            }

            // Tạo bản ghi navigation tương ứng
            Navigation::create([
                'navigationable_type' => Menu::class,
                'navigationable_id' => $menuId['id'],
                'order_id' => $navigationOrder,
                'parent_id' => $menu['parent_menu_id'] ?? null,
            ]);
        }
    }
}
