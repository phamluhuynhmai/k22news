<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Seeder này dùng để tạo dữ liệu mẫu cho bảng pages
 */
class DefaultPageSeeder extends Seeder
{
    /**
     * Chạy seeder để tạo dữ liệu mẫu.
     *
     * @return void
     */
    public function run()
    {
        // Mảng chứa dữ liệu mẫu cho các trang
        $pages = [
            [
                'name' => 'Olympics', // Tên trang
                'title' => 'Upcoming olympics', // Tiêu đề trang
                'slug' => 'upcoming-olympics', // Đường dẫn URL thân thiện
                'meta_title' => 'Everything about next olympics', // Tiêu đề meta cho SEO
                'meta_description' => 'Read about where and when will the next winter olympics be held ??', // Mô tả meta cho SEO
                'location' => 2, // Vị trí hiển thị của trang
                'Visibility' => 1, // Trạng thái hiển thị (1: hiện, 0: ẩn)
                'show_title' => 0, // Hiển thị tiêu đề (1: có, 0: không)
                'show_right_column' => 1, // Hiển thị cột phải (1: có, 0: không)
                'show_breadcrumb' => 0, // Hiển thị breadcrumb (1: có, 0: không)
                'permission' => 1, // Quyền truy cập
                'content' => "<p>The 2022 Beijing Winter Olympics will be spread over three distinct zones and merge new venues with existing ones from the 2008 Games, including the Bird's Nest stadium.</p>

<p>With 100 days to go, AFP Sport takes an in-depth look at where the Olympics will take place:</p>

<p>Beijing - The 80,000-seater Bird's Nest National Stadium -- whose cross-hatched steel girders produce a nest-like appearance -- will stage the opening and closing ceremony.</p>

<p>sting Olympic Park is a 12,000-capacity speed skating oval nicknamed the Ice Ribbon.</p>

<p>New venues have been built in other parts of the city, such as the striking location for some of the skiing and snowboarding events.</p>

<p>The 60-metre-high Big Air jumping platform stands in the shadow of four vast cooling towers at a former steel mill that once employed tens of thousands of workers and is now a trendy bar, restaurant and office complex. </p>
    </p>", // Nội dung trang
                'lang_id' => 1, // ID ngôn ngữ
                'parent_menu_link' => 2, // ID menu cha
            ],
            [
                'name' => 'future of gaming', // Tên trang
                'title' => 'technology used in gaming', // Tiêu đề trang
                'slug' => 'technology-used-in-gaming', // Đường dẫn URL thân thiện
                'meta_title' => 'Usage of new technology in gaming', // Tiêu đề meta cho SEO
                'meta_description' => 'Which new technology used in gaming Read now !!', // Mô tả meta cho SEO
                'location' => 4, // Vị trí hiển thị của trang
                'Visibility' => 1, // Trạng thái hiển thị (1: hiện, 0: ẩn)
                'show_title' => 1, // Hiển thị tiêu đề (1: có, 0: không)
                'show_right_column' => 0, // Hiển thị cột phải (1: có, 0: không)
                'show_breadcrumb' => 1, // Hiển thị breadcrumb (1: có, 0: không)
                'permission' => 1, // Quyền truy cập
                'content' => '22032022.jpg', // Nội dung trang (hình ảnh)
                'lang_id' => 2, // ID ngôn ngữ
                'parent_menu_link' => 3, // ID menu cha
            ],
        ];

        // Tạo các bản ghi trong bảng pages từ mảng dữ liệu mẫu
        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
