<?php

namespace App\Repositories;

use App\Models\Poll;

/**
 * Repository class để xử lý các thao tác với bảng Poll
 * Kế thừa từ BaseRepository để sử dụng các phương thức CRUD cơ bản
 */
class PollRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm trong bảng Poll
     * @var array
     */
    public $fieldSearchable = [
        'lang_id',    // ID của ngôn ngữ
        'question',   // Câu hỏi của cuộc thăm dò
    ];

    /**
     * Lấy danh sách các trường có thể tìm kiếm
     * @return array Mảng chứa tên các trường có thể tìm kiếm
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Xác định model được sử dụng cho repository này
     * @return string Tên class của model Poll
     */
    public function model()
    {
        return Poll::class;
    }
}
