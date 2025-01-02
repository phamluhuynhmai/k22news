<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Model Setting - Quản lý cài đặt hệ thống
 *
 * @property int $id ID của cài đặt
 * @property string $key Khóa cài đặt
 * @property string $value Giá trị cài đặt
 * @property Carbon|null $created_at Thời gian tạo
 * @property Carbon|null $updated_at Thời gian cập nhật
 *
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereKey($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting whereValue($value)
 * @mixin Eloquent
 *
 * @property-read MediaCollection|Media[] $media Tập hợp media files
 * @property-read int|null $media_count Số lượng media files
 */
class Setting extends Authenticatable implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    // Tên bảng trong database
    protected $table = 'settings';

    // Tự động load quan hệ media
    protected $with = ['media'];

    // Thêm các thuộc tính ảo
    protected $appends = ['logo', 'favicon'];

    /**
     * Các trường có thể gán giá trị hàng loạt
     * @var string[]
     */
    protected $fillable = [
        'key',
        'value',
    ];

    // Định nghĩa kiểu dữ liệu cho các trường
    protected $casts = [
        'key' => 'string',
        'value' => 'string',
    ];

    // Các hằng số cho giá trị Yes/No
    const Yes = 1;
    const No = 0;

    // Các hằng số cho loại media
    const LOGO = 'logo';
    const FAVICON = 'favicon';
    const IMAGE = 'image';

    // Các hằng số cho tần suất cập nhật RSS
    const EVERY_3_HOURS = 1;
    const TWICE_A_DAY = 2;
    const EVERY_DAY = 3;
    const WEEKLY = 4;

    // Mảng tên hiển thị cho các tần suất cập nhật RSS
    const AUTO_UPDATE_RSS_FEED = [
        self::EVERY_3_HOURS => 'Mỗi 3 Giờ',
        self::TWICE_A_DAY => 'Hai Lần Một Ngày',
        self::EVERY_DAY => 'Mỗi Ngày',
        self::WEEKLY => 'Hàng Tuần',
    ];

    // Mảng tên hàm tương ứng với các tần suất cập nhật
    const AUTO_UPDATE_RSS_FEED_FUNCTION = [
        self::EVERY_3_HOURS => 'everyThreeHours()',
        self::TWICE_A_DAY => 'twiceDaily()',
        self::EVERY_DAY => 'daily()',
        self::WEEKLY => 'weekly()',
    ];

    /**
     * Lấy đường dẫn đến logo
     * @return string URL của logo
     */
    public function getLogoAttribute()
    {
        /** @var Media $media */
        $media = $this->getMedia(self::LOGO)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('assets/image/k22-logo.png');
    }

    /**
     * Lấy đường dẫn đến favicon
     * @return string URL của favicon
     */
    public function getFaviconAttribute()
    {
        /** @var Media $media */
        $media = $this->getMedia(self::FAVICON)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('assets/image/favicon-k22.png');
    }
}
