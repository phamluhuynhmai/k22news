<?php

namespace App\Repositories;

use App\Models\Analytic;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Class DashboardRepository
 * Repository xử lý dữ liệu cho dashboard
 */
class DashboardRepository
{
    /**
     * Cập nhật dữ liệu biểu đồ theo khoảng thời gian
     * 
     * @param array $input Mảng dữ liệu đầu vào chứa start_date và end_date
     * @return array Mảng kết quả chứa data (số lượng analytics) và labels (ngày tháng)
     */
    public function updateChartRange($input)
    {
        // Lấy ngày bắt đầu từ input, nếu không có thì lấy 1 tháng trước
        $startDate = isset($input['start_date']) ? Carbon::parse($input['start_date']) : Carbon::now()->subMonth();
        
        // Lấy ngày kết thúc từ input, nếu không có thì lấy ngày hiện tại
        $endDate = isset($input['end_date']) ? Carbon::parse($input['end_date']) : Carbon::now();
        
        $result = [];
        
        // Tạo khoảng thời gian từ ngày bắt đầu đến ngày kết thúc
        $period = CarbonPeriod::create($startDate, $endDate);

        // Duyệt qua từng ngày trong khoảng thời gian
        foreach ($period as $date) {
            // Đếm số lượng analytics được tạo trong ngày
            $result['data'][] = Analytic::whereDate('created_at', $date)->count();
            
            // Thêm ngày vào mảng labels theo định dạng Y-m-d
            $result['labels'][] = $date->format('Y-m-d');
        }

        return $result;
    }
}
