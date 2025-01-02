<?php

namespace App\Repositories;

use App\Models\Plan;
use Illuminate\Support\Facades\DB;

/**
 * Class PlanRepository
 * Repository xử lý các thao tác liên quan đến model Plan (Gói dịch vụ)
 */
class PlanRepository extends BaseRepository
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
    public function getFieldsSearchable(): mixed
    {
        return $this->fieldSearchable;
    }

    /**
     * Xác định model được sử dụng cho repository này
     */
    public function model(): string
    {
        return Plan::class;
    }

    /**
     * Tạo mới một gói dịch vụ
     * 
     * @param array $input Dữ liệu đầu vào
     * @return Plan Model gói dịch vụ đã tạo
     * @throws UnprocessableEntityHttpException
     */
    public function store($input)
    {
        try {
            DB::beginTransaction();

            // Thiết lập số ngày dùng thử, mặc định là 0 nếu không được cung cấp
            $input['trial_days'] = $input['trial_days'] != null ? $input['trial_days'] : 0;
            // Xóa dấu phẩy từ giá tiền
            $input['price'] = removeCommaFromNumbers($input['price']);

            $plan = Plan::create($input);
            
            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin gói dịch vụ
     * 
     * @param array $input Dữ liệu cập nhật
     * @param int $id ID của gói dịch vụ cần cập nhật
     * @return array Dữ liệu đã cập nhật
     * @throws UnprocessableEntityHttpException
     */
    public function update($input, $id)
    {
        try {
            DB::beginTransaction();

            $plan = Plan::findOrFail($id);
            // Thiết lập số ngày dùng thử, mặc định là 0 nếu không được cung cấp
            $input['trial_days'] = $input['trial_days'] != null ? $input['trial_days'] : 0;
            // Xóa dấu phẩy từ giá tiền
            $input['price'] = removeCommaFromNumbers($input['price']);

            $plan->update($input);

            DB::commit();

            return $input;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
